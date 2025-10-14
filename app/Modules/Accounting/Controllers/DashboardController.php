<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Expense;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\Product;
use App\Modules\Accounting\Services\DashboardService;
use App\Modules\Accounting\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    protected ReportService $reportService;

    public function __construct(DashboardService $dashboardService, ReportService $reportService)
    {
        $this->dashboardService = $dashboardService;
        $this->reportService = $reportService;
    }

    /**
     * Display the accounting dashboard
     */
    public function index(): View
    {
        $dashboardData = $this->dashboardService->getSummaryStats();
        $dashboardData['net_income'] = $dashboardData['monthly_revenue'] - $dashboardData['monthly_expenses'];
        $dashboardData['recent_invoices'] = $this->dashboardService->getRecentInvoices(5);
        $dashboardData['recent_expenses'] = $this->dashboardService->getRecentExpenses(5);

        return view('modules.accounting.index', compact('dashboardData'));
    }

    /**
     * Display financial reports
     */
    public function reports(Request $request): View
    {
        $reportType = $request->get('type', 'profit_loss');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $reportData = match ($reportType) {
            'profit_loss' => $this->reportService->getProfitLossReport($startDate, $endDate),
            'balance_sheet' => $this->reportService->getBalanceSheetReport($endDate),
            'cash_flow' => $this->reportService->getCashFlowReport($startDate, $endDate),
            'trial_balance' => $this->reportService->getTrialBalanceReport($endDate),
            default => []
        };

        return view('modules.accounting.reports.index', compact('reportData', 'reportType', 'startDate', 'endDate'));
    }

    /**
     * Display chart of accounts
     */
    public function chartOfAccounts(Request $request): View
    {
        $query = ChartOfAccount::with('parentAccount')
            ->orderBy('account_code');

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%")
                    ->orWhere('account_code', 'like', "%{$search}%");
            });
        }

        $accounts = $query->get();

        $stats = [
            'total_accounts' => ChartOfAccount::count(),
            'active_accounts' => ChartOfAccount::active()->count(),
            'asset_accounts' => ChartOfAccount::byType('asset')->count(),
            'liability_accounts' => ChartOfAccount::byType('liability')->count(),
            'revenue_accounts' => ChartOfAccount::byType('revenue')->count(),
            'expense_accounts' => ChartOfAccount::byType('expense')->count(),
        ];

        return view('modules.accounting.chart-of-accounts.index', compact('accounts', 'stats'));
    }

    /**
     * Get dashboard data for AJAX requests
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        $period = $request->get('period', '30');
        $data = $this->dashboardService->getDashboardData($period);

        return response()->json($data);
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData(Request $request): JsonResponse
    {
        $type = $request->get('type', 'revenue_expense');
        $period = $request->get('period', '12');

        $data = match ($type) {
            'revenue_expense' => $this->dashboardService->getRevenueExpenseChartData($period),
            'invoice_status' => $this->dashboardService->getInvoiceStatusChartData(),
            'customer_balance' => $this->dashboardService->getCustomerBalanceChartData(),
            'expense_category' => $this->dashboardService->getExpenseCategoryChartData($period),
            default => []
        };

        return response()->json($data);
    }

    /**
     * Export report to PDF
     */
    public function exportReport(Request $request): \Illuminate\Http\Response
    {
        $reportType = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $pdf = $this->reportService->exportToPDF($reportType, $startDate, $endDate);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$reportType}_report.pdf\"");
    }

    /**
     * Search for customers, products, or accounts
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        $results = [];

        if ($type === 'all' || $type === 'customers') {
            $customers = Customer::search($query)->active()->limit(5)->get();
            $results['customers'] = $customers->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'text' => $customer->display_name,
                    'type' => 'customer',
                ];
            });
        }

        if ($type === 'all' || $type === 'products') {
            $products = Product::search($query)->active()->limit(5)->get();
            $results['products'] = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name,
                    'price' => $product->unit_price,
                    'type' => 'product',
                ];
            });
        }

        if ($type === 'all' || $type === 'accounts') {
            $accounts = ChartOfAccount::where('account_name', 'like', "%{$query}%")
                ->orWhere('account_code', 'like', "%{$query}%")
                ->active()
                ->limit(5)
                ->get();
            $results['accounts'] = $accounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'text' => $account->display_name,
                    'type' => 'account',
                ];
            });
        }

        return response()->json($results);
    }

    /**
     * Get quick stats for dashboard widgets
     */
    public function getQuickStats(): JsonResponse
    {
        $stats = [
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'total_expenses' => Expense::where('status', 'paid')->sum('total_amount'),
            'outstanding_invoices' => Invoice::whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])->sum('balance_due'),
            'overdue_invoices' => Invoice::where('status', 'overdue')->sum('balance_due'),
            'total_customers' => Customer::active()->count(),
            'total_products' => Product::active()->count(),
            'low_stock_items' => Product::lowStock()->count(),
            'recent_invoices' => Invoice::with('customer')->orderBy('created_at', 'desc')->limit(5)->get(),
            'recent_expenses' => Expense::with('vendor')->orderBy('created_at', 'desc')->limit(5)->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get financial summary for a specific period
     */
    public function getFinancialSummary(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $summary = [
            'revenue' => Invoice::whereBetween('invoice_date', [$startDate, $endDate])
                ->whereIn('status', ['paid', 'partial'])
                ->sum('paid_amount'),
            'expenses' => Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->where('status', 'paid')
                ->sum('total_amount'),
            'profit' => 0,
            'invoices_sent' => Invoice::whereBetween('invoice_date', [$startDate, $endDate])
                ->whereIn('status', ['sent', 'viewed', 'partial', 'paid'])
                ->count(),
            'invoices_paid' => Invoice::whereBetween('invoice_date', [$startDate, $endDate])
                ->where('status', 'paid')
                ->count(),
        ];

        $summary['profit'] = $summary['revenue'] - $summary['expenses'];

        return response()->json($summary);
    }
}
