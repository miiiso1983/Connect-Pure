<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Account;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\TaxRate;
use App\Modules\Accounting\Requests\InvoiceStoreRequest;
use App\Modules\Accounting\Requests\InvoiceUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['customer']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->orderBy('invoice_date', 'desc')
            ->paginate(20);

        $customers = Customer::active()->orderBy('name')->get();
        $statuses = Invoice::getStatuses();

        // Summary statistics
        $totalInvoices = $query->count();
        $totalAmount = $query->sum('total_amount');
        $paidAmount = $query->sum('paid_amount');
        $outstandingAmount = $query->sum('balance_due');

        $summary = [
            'total_invoices' => $totalInvoices,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => $outstandingAmount,
        ];

        return view('modules.accounting.invoices.index', compact(
            'invoices', 'customers', 'statuses', 'summary'
        ));
    }

    public function create()
    {
        $this->authorize('create', Invoice::class);

        $customers = Customer::active()->orderBy('name')->get();
        $accounts = Account::active()->byType('revenue')->orderBy('name')->get();
        $taxRates = TaxRate::active()->byType('sales')->get();

        return view('modules.accounting.invoices.create', compact(
            'customers', 'accounts', 'taxRates'
        ));
    }

    public function store(InvoiceStoreRequest $request)
    {
        $this->authorize('create', Invoice::class);

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'currency' => $validated['currency'],
                'exchange_rate' => $validated['exchange_rate'] ?? 1,
                'payment_terms' => $validated['payment_terms'] ?? 'net_30',
                'notes' => $validated['notes'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'billing_address' => $validated['billing_address'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'po_number' => $validated['po_number'] ?? null,
                'discount_amount' => $validated['discount_amount'] ?? 0,
            ]);

            // Add invoice items
            foreach ($validated['items'] as $index => $itemData) {
                $invoice->items()->create([
                    'item_type' => $itemData['item_type'] ?? 'service',
                    'name' => $itemData['name'],
                    'description' => $itemData['description'] ?? '',
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? 'each',
                    'rate' => $itemData['rate'],
                    'tax_rate' => $itemData['tax_rate'] ?? 0,
                    'discount_rate' => $itemData['discount_rate'] ?? 0,
                    'account_id' => $itemData['account_id'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }

            $invoice->calculateTotals();

            // Update customer balance
            $invoice->customer->updateBalance($invoice->total_amount);

            DB::commit();

            return redirect()->route('modules.accounting.invoices.show', $invoice)
                ->with('success', __('accounting.invoice_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput()
                ->withErrors(['error' => __('accounting.error_creating_invoice')]);
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.account', 'payments', 'paymentLinks']);

        return view('modules.accounting.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === 'paid') {
            return redirect()->route('modules.accounting.invoices.show', $invoice)
                ->withErrors(['error' => __('accounting.cannot_edit_paid_invoice')]);
        }

        $invoice->load(['items']);
        $customers = Customer::active()->orderBy('name')->get();
        $accounts = Account::active()->byType('revenue')->orderBy('name')->get();
        $taxRates = TaxRate::active()->byType('sales')->get();

        return view('modules.accounting.invoices.edit', compact(
            'invoice', 'customers', 'accounts', 'taxRates'
        ));
    }

    public function update(InvoiceUpdateRequest $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === 'paid') {
            return redirect()->route('modules.accounting.invoices.show', $invoice)
                ->withErrors(['error' => __('accounting.cannot_edit_paid_invoice')]);
        }

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Update customer balance (remove old amount)
            $invoice->customer->updateBalance(-$invoice->total_amount);

            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'currency' => $validated['currency'],
                'exchange_rate' => $validated['exchange_rate'] ?? 1,
                'payment_terms' => $validated['payment_terms'] ?? 'net_30',
                'notes' => $validated['notes'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'billing_address' => $validated['billing_address'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'po_number' => $validated['po_number'] ?? null,
                'discount_amount' => $validated['discount_amount'] ?? 0,
            ]);

            // Delete existing items and recreate
            $invoice->items()->delete();

            foreach ($validated['items'] as $index => $itemData) {
                $invoice->items()->create([
                    'item_type' => $itemData['item_type'] ?? 'service',
                    'name' => $itemData['name'],
                    'description' => $itemData['description'] ?? '',
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? 'each',
                    'rate' => $itemData['rate'],
                    'tax_rate' => $itemData['tax_rate'] ?? 0,
                    'discount_rate' => $itemData['discount_rate'] ?? 0,
                    'account_id' => $itemData['account_id'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }

            $invoice->calculateTotals();

            // Update customer balance (add new amount)
            $invoice->customer->updateBalance($invoice->total_amount);

            DB::commit();

            return redirect()->route('modules.accounting.invoices.show', $invoice)
                ->with('success', __('accounting.invoice_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput()
                ->withErrors(['error' => __('accounting.error_updating_invoice')]);
        }
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);

        if ($invoice->status === 'paid' || $invoice->paid_amount > 0) {
            return redirect()->route('modules.accounting.invoices.index')
                ->withErrors(['error' => __('accounting.cannot_delete_paid_invoice')]);
        }

        DB::beginTransaction();
        try {
            // Update customer balance
            $invoice->customer->updateBalance(-$invoice->total_amount);

            $invoice->delete();

            DB::commit();

            return redirect()->route('modules.accounting.invoices.index')
                ->with('success', __('accounting.invoice_deleted_successfully'));

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('modules.accounting.invoices.index')
                ->withErrors(['error' => __('accounting.error_deleting_invoice')]);
        }
    }

    public function send(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if ($invoice->status === 'draft') {
            $invoice->markAsSent();

            // Fire the InvoiceSubmitted event to trigger WhatsApp notification
            event(new \App\Events\InvoiceSubmitted($invoice));

            // Here you would typically send the invoice via email
            // Mail::to($invoice->customer->email)->send(new InvoiceMail($invoice));

            return redirect()->route('modules.accounting.invoices.show', $invoice)
                ->with('success', __('accounting.invoice_sent_successfully_with_whatsapp'));
        }

        return redirect()->route('modules.accounting.invoices.show', $invoice)
            ->withErrors(['error' => __('accounting.invoice_already_sent')]);
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $request->validate([
            'payment_amount' => 'required|numeric|min:0.01|max:'.$invoice->balance_due,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $invoice->addPayment($request->payment_amount, [
                'payment_date' => $request->payment_date,
                'method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('modules.accounting.invoices.show', $invoice)
                ->with('success', __('accounting.payment_recorded_successfully'));

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput()
                ->withErrors(['error' => __('accounting.error_recording_payment')]);
        }
    }

    public function duplicate(Invoice $invoice)
    {
        $this->authorize('create', Invoice::class);

        $newInvoice = $invoice->replicate();
        $newInvoice->invoice_number = null; // Will be auto-generated
        $newInvoice->status = 'draft';
        $newInvoice->invoice_date = now();
        $newInvoice->due_date = now()->addDays(30);
        $newInvoice->paid_amount = 0;
        $newInvoice->balance_due = 0;
        $newInvoice->sent_at = null;
        $newInvoice->viewed_at = null;
        $newInvoice->paid_at = null;
        $newInvoice->save();

        // Duplicate items
        foreach ($invoice->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $newInvoice->id;
            $newItem->save();
        }

        $newInvoice->calculateTotals();

        return redirect()->route('modules.accounting.invoices.edit', $newInvoice)
            ->with('success', __('accounting.invoice_duplicated_successfully'));
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['customer', 'items']);

        // Here you would generate PDF using a library like DomPDF or wkhtmltopdf
        // For now, return a view that can be printed
        return view('modules.accounting.invoices.pdf', compact('invoice'));
    }

    public function createPaymentLink(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $service = app(\App\Modules\Accounting\Services\PaymentLinkService::class);
        $link = $service->createForInvoice($invoice);
        $url = $service->buildUrl($link);

        return redirect()->route('modules.accounting.invoices.show', $invoice)
            ->with('success', __('accounting.payment_link_created'))
            ->with('payment_link_url', $url);
    }
}
