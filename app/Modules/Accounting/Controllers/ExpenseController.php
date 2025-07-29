<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Expense;
use App\Modules\Accounting\Models\Vendor;
use App\Modules\Accounting\Models\Employee;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['vendor', 'employee', 'customer', 'account']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                      $vendorQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $expenses = $query->orderBy('expense_date', 'desc')
                         ->paginate(20);

        $vendors = Vendor::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        $statuses = Expense::getStatuses();
        $categories = Expense::getCategories();

        // Summary statistics
        $totalExpenses = $query->count();
        $totalAmount = $query->sum('total_amount');
        $paidAmount = $query->where('status', 'paid')->sum('total_amount');
        $pendingAmount = $query->whereIn('status', ['draft', 'pending', 'approved'])->sum('total_amount');

        $summary = [
            'total_expenses' => $totalExpenses,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'pending_amount' => $pendingAmount,
        ];

        return view('modules.accounting.expenses.index', compact(
            'expenses', 'vendors', 'employees', 'statuses', 'categories', 'summary'
        ));
    }

    public function create()
    {
        $vendors = Vendor::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        $customers = Customer::active()->orderBy('name')->get();
        $accounts = Account::active()->byType('expense')->orderBy('name')->get();
        $categories = Expense::getCategories();
        $paymentMethods = Expense::getPaymentMethods();

        return view('modules.accounting.expenses.create', compact(
            'vendors', 'employees', 'customers', 'accounts', 'categories', 'paymentMethods'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'account_id' => 'required|exists:accounting_accounts,id',
        ]);

        DB::beginTransaction();
        try {
            $expense = Expense::create([
                'vendor_id' => $request->vendor_id,
                'employee_id' => $request->employee_id,
                'customer_id' => $request->customer_id,
                'account_id' => $request->account_id,
                'expense_date' => $request->expense_date,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'exchange_rate' => $request->exchange_rate ?? 1,
                'tax_amount' => $request->tax_amount ?? 0,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'receipt_number' => $request->receipt_number,
                'is_billable' => $request->boolean('is_billable'),
                'is_reimbursable' => $request->boolean('is_reimbursable'),
                'notes' => $request->notes,
                'status' => $request->status ?? 'draft',
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('expenses/' . $expense->id, 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
                $expense->update(['attachments' => $attachments]);
            }

            DB::commit();

            return redirect()->route('modules.accounting.expenses.show', $expense)
                           ->with('success', __('accounting.expense_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => __('accounting.error_creating_expense')]);
        }
    }

    public function show(Expense $expense)
    {
        $expense->load(['vendor', 'employee', 'customer', 'account', 'payments']);
        
        return view('modules.accounting.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        if ($expense->status === 'paid') {
            return redirect()->route('modules.accounting.expenses.show', $expense)
                           ->withErrors(['error' => __('accounting.cannot_edit_paid_expense')]);
        }

        $vendors = Vendor::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        $customers = Customer::active()->orderBy('name')->get();
        $accounts = Account::active()->byType('expense')->orderBy('name')->get();
        $categories = Expense::getCategories();
        $paymentMethods = Expense::getPaymentMethods();

        return view('modules.accounting.expenses.edit', compact(
            'expense', 'vendors', 'employees', 'customers', 'accounts', 'categories', 'paymentMethods'
        ));
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->status === 'paid') {
            return redirect()->route('modules.accounting.expenses.show', $expense)
                           ->withErrors(['error' => __('accounting.cannot_edit_paid_expense')]);
        }

        $request->validate([
            'expense_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'account_id' => 'required|exists:accounting_accounts,id',
        ]);

        DB::beginTransaction();
        try {
            $expense->update([
                'vendor_id' => $request->vendor_id,
                'employee_id' => $request->employee_id,
                'customer_id' => $request->customer_id,
                'account_id' => $request->account_id,
                'expense_date' => $request->expense_date,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'exchange_rate' => $request->exchange_rate ?? 1,
                'tax_amount' => $request->tax_amount ?? 0,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'receipt_number' => $request->receipt_number,
                'is_billable' => $request->boolean('is_billable'),
                'is_reimbursable' => $request->boolean('is_reimbursable'),
                'notes' => $request->notes,
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                $attachments = $expense->attachments ?? [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('expenses/' . $expense->id, 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
                $expense->update(['attachments' => $attachments]);
            }

            DB::commit();

            return redirect()->route('modules.accounting.expenses.show', $expense)
                           ->with('success', __('accounting.expense_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => __('accounting.error_updating_expense')]);
        }
    }

    public function destroy(Expense $expense)
    {
        if ($expense->status === 'paid') {
            return redirect()->route('modules.accounting.expenses.index')
                           ->withErrors(['error' => __('accounting.cannot_delete_paid_expense')]);
        }

        DB::beginTransaction();
        try {
            $expense->delete();
            
            DB::commit();

            return redirect()->route('modules.accounting.expenses.index')
                           ->with('success', __('accounting.expense_deleted_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('modules.accounting.expenses.index')
                           ->withErrors(['error' => __('accounting.error_deleting_expense')]);
        }
    }

    public function approve(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return redirect()->route('modules.accounting.expenses.show', $expense)
                           ->withErrors(['error' => __('accounting.expense_not_pending')]);
        }

        $expense->approve(auth()->id());

        return redirect()->route('modules.accounting.expenses.show', $expense)
                       ->with('success', __('accounting.expense_approved_successfully'));
    }

    public function reject(Expense $expense)
    {
        if ($expense->status !== 'pending') {
            return redirect()->route('modules.accounting.expenses.show', $expense)
                           ->withErrors(['error' => __('accounting.expense_not_pending')]);
        }

        $expense->reject();

        return redirect()->route('modules.accounting.expenses.show', $expense)
                       ->with('success', __('accounting.expense_rejected_successfully'));
    }

    public function markAsPaid(Request $request, Expense $expense)
    {
        if ($expense->status !== 'approved') {
            return redirect()->route('modules.accounting.expenses.show', $expense)
                           ->withErrors(['error' => __('accounting.expense_not_approved')]);
        }

        $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $expense->markAsPaid([
                'payment_date' => $request->payment_date,
                'method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('modules.accounting.expenses.show', $expense)
                           ->with('success', __('accounting.expense_marked_paid_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->withErrors(['error' => __('accounting.error_marking_expense_paid')]);
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'expense_ids' => 'required|array',
            'expense_ids.*' => 'exists:accounting_expenses,id',
        ]);

        $expenses = Expense::whereIn('id', $request->expense_ids)
                          ->where('status', 'pending')
                          ->get();

        foreach ($expenses as $expense) {
            $expense->approve(auth()->id());
        }

        return redirect()->route('modules.accounting.expenses.index')
                       ->with('success', __('accounting.expenses_approved_successfully', ['count' => $expenses->count()]));
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'expense_ids' => 'required|array',
            'expense_ids.*' => 'exists:accounting_expenses,id',
        ]);

        $expenses = Expense::whereIn('id', $request->expense_ids)
                          ->where('status', 'pending')
                          ->get();

        foreach ($expenses as $expense) {
            $expense->reject();
        }

        return redirect()->route('modules.accounting.expenses.index')
                       ->with('success', __('accounting.expenses_rejected_successfully', ['count' => $expenses->count()]));
    }
}
