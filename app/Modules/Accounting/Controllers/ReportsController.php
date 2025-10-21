<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Expense;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Quick stats for dashboard
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $stats = [
            'total_revenue' => Invoice::where('status', 'paid')
                ->whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                ->sum('total_amount'),
            'total_expenses' => Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
                ->sum('amount'),
            'outstanding_invoices' => Invoice::whereIn('status', ['sent', 'partial'])
                ->sum('total_amount'),
            'monthly_profit' => 0, // Will be calculated
        ];

        $stats['monthly_profit'] = $stats['total_revenue'] - $stats['total_expenses'];

        // Monthly revenue trend (last 12 months)  DB-agnostic (no strftime)
        $monthlyRevenue = collect();
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $sum = Invoice::where('status', 'paid')
                ->whereYear('invoice_date', $d->year)
                ->whereMonth('invoice_date', $d->month)
                ->sum('total_amount');
            $monthlyRevenue->push((object) [
                'year' => (string) $d->year,
                'month' => str_pad((string) $d->month, 2, '0', STR_PAD_LEFT),
                'revenue' => (float) $sum,
            ]);
        }

        // Top customers by revenue
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();

        $topCustomers = Customer::withSum(['invoices' => function ($query) use ($startOfYear, $endOfYear) {
            $query->where('status', 'paid')
                ->whereBetween('invoice_date', [$startOfYear, $endOfYear]);
        }], 'total_amount')
            ->orderByDesc('invoices_sum_total_amount')
            ->take(5)
            ->get();

        return view('modules.accounting.reports.index', compact(
            'stats', 'monthlyRevenue', 'topCustomers'
        ));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Revenue
        $revenue = Invoice::where('status', 'paid')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->selectRaw('SUM(total_amount) as total, COUNT(*) as count')
            ->first();

        // Expenses by category
        $expensesByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $totalExpenses = $expensesByCategory->sum('total');
        $netProfit = ($revenue->total ?? 0) - $totalExpenses;
        $profitMargin = $revenue->total > 0 ? ($netProfit / $revenue->total) * 100 : 0;

        // Monthly breakdown - using separate queries for SQLite compatibility
        $monthlyRevenue = Invoice::where('status', 'paid')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->selectRaw("strftime('%Y', invoice_date) as year, strftime('%m', invoice_date) as month, SUM(total_amount) as revenue")
            ->groupByRaw("strftime('%Y', invoice_date), strftime('%m', invoice_date)")
            ->get()
            ->keyBy(function ($item) {
                return $item->year.'-'.str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $monthlyExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw("strftime('%Y', expense_date) as year, strftime('%m', expense_date) as month, SUM(amount) as expenses")
            ->groupByRaw("strftime('%Y', expense_date), strftime('%m', expense_date)")
            ->get()
            ->keyBy(function ($item) {
                return $item->year.'-'.str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        // Combine revenue and expenses data
        $monthlyBreakdown = collect();
        $allMonths = $monthlyRevenue->keys()->merge($monthlyExpenses->keys())->unique()->sort();

        foreach ($allMonths as $monthKey) {
            $revenue = $monthlyRevenue->get($monthKey);
            $expense = $monthlyExpenses->get($monthKey);

            $monthlyBreakdown->push((object) [
                'year' => substr($monthKey, 0, 4),
                'month' => substr($monthKey, 5, 2),
                'revenue' => $revenue ? $revenue->revenue : 0,
                'expenses' => $expense ? $expense->expenses : 0,
            ]);
        }

        return view('modules.accounting.reports.profit-loss', compact(
            'revenue', 'expensesByCategory', 'totalExpenses', 'netProfit',
            'profitMargin', 'monthlyBreakdown', 'startDate', 'endDate'
        ));
    }

    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->get('as_of_date', now()->format('Y-m-d'));

        // Assets
        $assets = [
            'cash' => $this->calculateCashBalance($asOfDate),
            'accounts_receivable' => $this->calculateAccountsReceivable($asOfDate),
            'inventory' => 0, // Placeholder for inventory if implemented
            'fixed_assets' => 0, // Placeholder for fixed assets
        ];

        $totalAssets = array_sum($assets);

        // Liabilities
        $liabilities = [
            'accounts_payable' => $this->calculateAccountsPayable($asOfDate),
            'accrued_expenses' => 0, // Placeholder
            'short_term_debt' => 0, // Placeholder
            'long_term_debt' => 0, // Placeholder
        ];

        $totalLiabilities = array_sum($liabilities);

        // Equity
        $equity = [
            'retained_earnings' => $this->calculateRetainedEarnings($asOfDate),
            'current_year_earnings' => $this->calculateCurrentYearEarnings($asOfDate),
        ];

        $totalEquity = array_sum($equity);

        return view('modules.accounting.reports.balance-sheet', compact(
            'assets', 'liabilities', 'equity', 'totalAssets',
            'totalLiabilities', 'totalEquity', 'asOfDate'
        ));
    }

    public function cashFlow(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Operating Activities
        $operatingCashFlow = [
            'cash_from_customers' => Invoice::where('status', 'paid')
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->sum('total_amount'),
            'cash_to_suppliers' => Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->where('category', 'supplies')
                ->sum('amount'),
            'operating_expenses' => Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->whereNotIn('category', ['supplies', 'equipment'])
                ->sum('amount'),
        ];

        $netOperatingCashFlow = $operatingCashFlow['cash_from_customers'] -
                               $operatingCashFlow['cash_to_suppliers'] -
                               $operatingCashFlow['operating_expenses'];

        // Investing Activities
        $investingCashFlow = [
            'equipment_purchases' => Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->where('category', 'equipment')
                ->sum('amount'),
        ];

        $netInvestingCashFlow = -$investingCashFlow['equipment_purchases'];

        // Financing Activities (placeholder)
        $financingCashFlow = [
            'loans_received' => 0,
            'loan_payments' => 0,
            'owner_investments' => 0,
            'owner_withdrawals' => 0,
        ];

        $netFinancingCashFlow = $financingCashFlow['loans_received'] +
                               $financingCashFlow['owner_investments'] -
                               $financingCashFlow['loan_payments'] -
                               $financingCashFlow['owner_withdrawals'];

        $netCashFlow = $netOperatingCashFlow + $netInvestingCashFlow + $netFinancingCashFlow;

        // Daily cash flow for chart
        $dailyCashFlow = DB::table(DB::raw('(
            SELECT invoice_date as date, total_amount as amount FROM accounting_invoices WHERE status = "paid"
            UNION ALL
            SELECT expense_date as date, -amount as amount FROM accounting_expenses
        ) as transactions'))
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(amount) as net_flow')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('modules.accounting.reports.cash-flow', compact(
            'operatingCashFlow', 'investingCashFlow', 'financingCashFlow',
            'netOperatingCashFlow', 'netInvestingCashFlow', 'netFinancingCashFlow',
            'netCashFlow', 'dailyCashFlow', 'startDate', 'endDate'
        ));
    }

    public function customerReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $customers = Customer::withSum(['invoices' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'paid')
                ->whereBetween('invoice_date', [$startDate, $endDate]);
        }], 'total_amount')
            ->withSum(['invoices' => function ($query) {
                $query->whereIn('status', ['sent', 'partial']);
            }], 'total_amount as outstanding_amount')
            ->withCount(['invoices' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('invoice_date', [$startDate, $endDate]);
            }])
            ->orderByDesc('invoices_sum_total_amount')
            ->paginate(20);

        return view('modules.accounting.reports.customers', compact(
            'customers', 'startDate', 'endDate'
        ));
    }

    public function vendorReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $vendors = Vendor::withSum(['expenses' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }], 'amount')
            ->withCount(['expenses' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('expense_date', [$startDate, $endDate]);
            }])
            ->orderByDesc('expenses_sum_amount')
            ->paginate(20);

        return view('modules.accounting.reports.vendors', compact(
            'vendors', 'startDate', 'endDate'
        ));
    }

    public function export(Request $request)
    {
        $reportType = $request->get('type');
        $format = $request->get('format', 'pdf');

        switch ($reportType) {
            case 'profit-loss':
                return $this->exportProfitLoss($request, $format);
            case 'balance-sheet':
                return $this->exportBalanceSheet($request, $format);
            case 'cash-flow':
                return $this->exportCashFlow($request, $format);
            default:
                return back()->with('error', __('accounting.invalid_report_type'));
        }
    }

    private function calculateCashBalance($asOfDate)
    {
        $revenue = Invoice::where('status', 'paid')
            ->where('invoice_date', '<=', $asOfDate)
            ->sum('total_amount');

        $expenses = Expense::where('expense_date', '<=', $asOfDate)
            ->sum('amount');

        return $revenue - $expenses;
    }

    private function calculateAccountsReceivable($asOfDate)
    {
        return Invoice::whereIn('status', ['sent', 'partial'])
            ->where('invoice_date', '<=', $asOfDate)
            ->sum('total_amount');
    }

    private function calculateAccountsPayable($asOfDate)
    {
        // This would need to be implemented based on your vendor payment system
        return 0;
    }

    private function calculateRetainedEarnings($asOfDate)
    {
        $startOfYear = Carbon::parse($asOfDate)->startOfYear();

        $revenue = Invoice::where('status', 'paid')
            ->where('invoice_date', '<', $startOfYear)
            ->sum('total_amount');

        $expenses = Expense::where('expense_date', '<', $startOfYear)
            ->sum('amount');

        return $revenue - $expenses;
    }

    private function calculateCurrentYearEarnings($asOfDate)
    {
        $startOfYear = Carbon::parse($asOfDate)->startOfYear();

        $revenue = Invoice::where('status', 'paid')
            ->whereBetween('invoice_date', [$startOfYear, $asOfDate])
            ->sum('total_amount');

        $expenses = Expense::whereBetween('expense_date', [$startOfYear, $asOfDate])
            ->sum('amount');

        return $revenue - $expenses;
    }

    private function exportProfitLoss($request, $format)
    {
        // Implementation for exporting P&L report
        $data = $this->profitLoss($request);

        if ($format === 'pdf') {
            $pdf = \PDF::loadView('modules.accounting.reports.exports.profit-loss-pdf', $data);

            return $pdf->download('profit-loss-report.pdf');
        }

        // CSV export logic here
        return response()->json(['message' => 'CSV export not implemented yet']);
    }

    private function exportBalanceSheet($request, $format)
    {
        // Implementation for exporting Balance Sheet
        return response()->json(['message' => 'Balance Sheet export not implemented yet']);
    }

    private function exportCashFlow($request, $format)
    {
        // Implementation for exporting Cash Flow
        return response()->json(['message' => 'Cash Flow export not implemented yet']);
    }
}
