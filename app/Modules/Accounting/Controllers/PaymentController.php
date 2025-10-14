<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['invoice', 'customer'])->paginate(15);

        return view('modules.accounting.payments.index', compact('payments'));
    }

    public function create()
    {
        $invoices = Invoice::where('status', '!=', 'paid')->get();
        $customers = Customer::all();

        return view('modules.accounting.payments.create', compact('invoices', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create($validated);

        return redirect()->route('modules.accounting.payments.index')
            ->with('success', 'Payment created successfully.');
    }

    public function show(Payment $payment)
    {
        return view('modules.accounting.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $invoices = Invoice::where('status', '!=', 'paid')->get();
        $customers = Customer::all();

        return view('modules.accounting.payments.edit', compact('payment', 'invoices', 'customers'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        return redirect()->route('modules.accounting.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('modules.accounting.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    public function void(Payment $payment)
    {
        $payment->update(['status' => 'voided']);

        return redirect()->route('modules.accounting.payments.show', $payment)
            ->with('success', 'Payment voided successfully.');
    }
}
