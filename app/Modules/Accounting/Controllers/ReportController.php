<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\Expense;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Vendor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('modules.accounting.reports.index');
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $revenue = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('total_amount');

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('amount');

        $netIncome = $revenue - $expenses;

        return view('modules.accounting.reports.profit-loss', compact(
            'revenue', 'expenses', 'netIncome', 'startDate', 'endDate'
        ));
    }

    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->get('as_of_date', now());

        // Simplified balance sheet calculation
        $assets = 100000; // Placeholder
        $liabilities = 50000; // Placeholder
        $equity = $assets - $liabilities;

        return view('modules.accounting.reports.balance-sheet', compact(
            'assets', 'liabilities', 'equity', 'asOfDate'
        ));
    }

    public function cashFlow(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $cashInflows = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('total_amount');

        $cashOutflows = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('amount');

        $netCashFlow = $cashInflows - $cashOutflows;

        return view('modules.accounting.reports.cash-flow', compact(
            'cashInflows', 'cashOutflows', 'netCashFlow', 'startDate', 'endDate'
        ));
    }

    public function trialBalance(Request $request)
    {
        $asOfDate = $request->get('as_of_date', now());

        // Placeholder trial balance data
        $accounts = [];

        return view('modules.accounting.reports.trial-balance', compact('accounts', 'asOfDate'));
    }

    public function generalLedger(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Placeholder general ledger data
        $entries = [];

        return view('modules.accounting.reports.general-ledger', compact('entries', 'startDate', 'endDate'));
    }

    public function customerAging(Request $request)
    {
        $asOfDate = $request->get('as_of_date', now());

        $customers = Customer::with(['invoices' => function($query) use ($asOfDate) {
            $query->where('status', '!=', 'paid')
                  ->where('due_date', '<=', $asOfDate);
        }])->get();

        return view('modules.accounting.reports.customer-aging', compact('customers', 'asOfDate'));
    }

    public function customerStatements(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $customers = Customer::with(['invoices' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('invoice_date', [$startDate, $endDate]);
        }])->get();

        return view('modules.accounting.reports.customer-statements', compact('customers', 'startDate', 'endDate'));
    }

    public function salesByCustomer(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $salesData = Customer::withSum(['invoices' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('invoice_date', [$startDate, $endDate])
                  ->where('status', 'paid');
        }], 'total_amount')->get();

        return view('modules.accounting.reports.sales-by-customer', compact('salesData', 'startDate', 'endDate'));
    }

    public function vendorAging(Request $request)
    {
        $asOfDate = $request->get('as_of_date', now());

        $vendors = Vendor::with(['expenses' => function($query) use ($asOfDate) {
            $query->where('status', '!=', 'paid')
                  ->where('due_date', '<=', $asOfDate);
        }])->get();

        return view('modules.accounting.reports.vendor-aging', compact('vendors', 'asOfDate'));
    }

    public function expensesByVendor(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $expenseData = Vendor::withSum(['expenses' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate])
                  ->where('status', 'paid');
        }], 'amount')->get();

        return view('modules.accounting.reports.expenses-by-vendor', compact('expenseData', 'startDate', 'endDate'));
    }

    public function inventoryValuation(Request $request)
    {
        $asOfDate = $request->get('as_of_date', now());

        // Placeholder inventory data
        $inventory = [];

        return view('modules.accounting.reports.inventory-valuation', compact('inventory', 'asOfDate'));
    }

    public function salesByProduct(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Placeholder product sales data
        $productSales = [];

        return view('modules.accounting.reports.sales-by-product', compact('productSales', 'startDate', 'endDate'));
    }

    public function salesTax(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $taxData = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->selectRaw('SUM(tax_amount) as total_tax, COUNT(*) as invoice_count')
            ->first();

        return view('modules.accounting.reports.sales-tax', compact('taxData', 'startDate', 'endDate'));
    }

    public function purchaseTax(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $taxData = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('SUM(tax_amount) as total_tax, COUNT(*) as expense_count')
            ->first();

        return view('modules.accounting.reports.purchase-tax', compact('taxData', 'startDate', 'endDate'));
    }

    public function export(Request $request, $type)
    {
        // Placeholder export functionality
        return response()->json(['message' => 'Export functionality not implemented yet']);
    }

    public function download(Request $request, $file)
    {
        // Placeholder download functionality
        return response()->json(['message' => 'Download functionality not implemented yet']);
    }
}
