<?php

namespace App\Modules\Accounting\Services;

use App\Modules\Accounting\Models\Customer;
use App\Modules\Accounting\Models\Expense;
use App\Modules\Accounting\Models\Invoice;
use App\Modules\Accounting\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get comprehensive dashboard data
     */
    public function getDashboardData(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        return [
            'summary_stats' => $this->getSummaryStats(),
            'financial_overview' => $this->getFinancialOverview($startDate, $endDate),
            'recent_invoices' => $this->getRecentInvoices(),
            'recent_expenses' => $this->getRecentExpenses(),
            'overdue_invoices' => $this->getOverdueInvoices(),
            'top_customers' => $this->getTopCustomers(),
            'cash_flow_chart' => $this->getCashFlowChartData($days),
            'revenue_expense_chart' => $this->getRevenueExpenseChartData(12),
            'invoice_status_chart' => $this->getInvoiceStatusChartData(),
            'expense_category_chart' => $this->getExpenseCategoryChartData($days),
            'alerts' => $this->getAlerts(),
        ];
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats(): array
    {
        return [
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'monthly_revenue' => Invoice::where('status', 'paid')
                ->whereBetween('invoice_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total_amount'),
            'total_expenses' => Expense::where('status', 'paid')->sum('total_amount'),
            'monthly_expenses' => Expense::where('status', 'paid')
                ->whereBetween('expense_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total_amount'),
            'outstanding_invoices' => Invoice::whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])
                ->sum('balance_due'),
            'overdue_amount' => Invoice::where('status', 'overdue')->sum('balance_due'),
            'total_customers' => Customer::active()->count(),
            'total_products' => Product::active()->count(),
            'draft_invoices' => Invoice::where('status', 'draft')->count(),
            'pending_expenses' => Expense::where('status', 'pending')->count(),
        ];
    }

    /**
     * Get financial overview for a period
     */
    public function getFinancialOverview(Carbon $startDate, Carbon $endDate): array
    {
        $revenue = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->whereIn('status', ['paid', 'partial'])
            ->sum('paid_amount');

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('total_amount');

        $profit = $revenue - $expenses;
        $profitMargin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $profit,
            'profit_margin' => $profitMargin,
            'invoices_sent' => Invoice::whereBetween('invoice_date', [$startDate, $endDate])
                ->whereIn('status', ['sent', 'viewed', 'partial', 'paid'])
                ->count(),
            'invoices_paid' => Invoice::whereBetween('invoice_date', [$startDate, $endDate])
                ->where('status', 'paid')
                ->count(),
        ];
    }

    /**
     * Get recent invoices
     */
    public function getRecentInvoices(int $limit = 10): Collection
    {
        return Invoice::with(['customer'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent expenses
     */
    public function getRecentExpenses(int $limit = 10): Collection
    {
        return Expense::with(['vendor'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices(): Collection
    {
        return Invoice::with(['customer'])
            ->where('status', 'overdue')
            ->orWhere(function ($query) {
                $query->whereIn('status', ['sent', 'viewed', 'partial'])
                    ->where('due_date', '<', now());
            })
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get top customers by revenue
     */
    public function getTopCustomers(int $limit = 10): Collection
    {
        return Customer::withSum(['invoices' => function ($query) {
            $query->where('status', 'paid');
        }], 'total_amount')
            ->orderBy('invoices_sum_total_amount', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get cash flow chart data
     */
    public function getCashFlowChartData(int $days = 30): array
    {
        $dates = collect();
        $inflows = collect();
        $outflows = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dates->push($date->format('M d'));

            // Inflows (payments received)
            $inflow = Invoice::where('status', 'paid')
                ->whereDate('updated_at', $date)
                ->sum('paid_amount');
            $inflows->push($inflow);

            // Outflows (expenses paid)
            $outflow = Expense::where('status', 'paid')
                ->whereDate('expense_date', $date)
                ->sum('total_amount');
            $outflows->push($outflow);
        }

        return [
            'labels' => $dates->toArray(),
            'inflows' => $inflows->toArray(),
            'outflows' => $outflows->toArray(),
        ];
    }

    /**
     * Get revenue vs expense chart data
     */
    public function getRevenueExpenseChartData(int $months = 12): array
    {
        $labels = collect();
        $revenue = collect();
        $expenses = collect();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels->push($date->format('M Y'));

            $startOfMonth = $date->startOfMonth();
            $endOfMonth = $date->endOfMonth();

            $monthRevenue = Invoice::whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                ->whereIn('status', ['paid', 'partial'])
                ->sum('paid_amount');
            $revenue->push($monthRevenue);

            $monthExpenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
                ->where('status', 'paid')
                ->sum('total_amount');
            $expenses->push($monthExpenses);
        }

        return [
            'labels' => $labels->toArray(),
            'revenue' => $revenue->toArray(),
            'expenses' => $expenses->toArray(),
        ];
    }

    /**
     * Get invoice status distribution chart data
     */
    public function getInvoiceStatusChartData(): array
    {
        $statuses = Invoice::selectRaw('status, COUNT(*) as count, SUM(total_amount) as amount')
            ->groupBy('status')
            ->get();

        return [
            'labels' => $statuses->pluck('status')->map(function ($status) {
                return __('accounting.'.$status);
            })->toArray(),
            'counts' => $statuses->pluck('count')->toArray(),
            'amounts' => $statuses->pluck('amount')->toArray(),
        ];
    }

    /**
     * Get expense category chart data
     */
    public function getExpenseCategoryChartData(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $categories = Expense::with('expenseAccount')
            ->where('expense_date', '>=', $startDate)
            ->where('status', 'paid')
            ->get()
            ->groupBy('expenseAccount.account_name')
            ->map(function ($expenses) {
                return $expenses->sum('total_amount');
            });

        return [
            'labels' => $categories->keys()->toArray(),
            'amounts' => $categories->values()->toArray(),
        ];
    }

    /**
     * Get customer balance distribution
     */
    public function getCustomerBalanceChartData(): array
    {
        $customers = Customer::where('current_balance', '>', 0)
            ->orderBy('current_balance', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $customers->pluck('display_name')->toArray(),
            'balances' => $customers->pluck('current_balance')->toArray(),
        ];
    }

    /**
     * Get system alerts and notifications
     */
    public function getAlerts(): array
    {
        $alerts = [];

        // Overdue invoices
        $overdueCount = Invoice::where('status', 'overdue')->count();
        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => __('accounting.overdue_invoices'),
                'message' => __('accounting.overdue_invoices_message', ['count' => $overdueCount]),
                'action_url' => route('modules.accounting.invoices.index', ['status' => 'overdue']),
                'action_text' => __('accounting.view_overdue'),
            ];
        }

        // Low stock products
        $lowStockCount = Product::lowStock()->count();
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => __('accounting.low_stock_alert'),
                'message' => __('accounting.low_stock_message', ['count' => $lowStockCount]),
                'action_url' => route('modules.accounting.products.index', ['stock' => 'low']),
                'action_text' => __('accounting.view_products'),
            ];
        }

        // Pending expenses
        $pendingExpensesCount = Expense::where('status', 'pending')->count();
        if ($pendingExpensesCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => __('accounting.pending_expenses'),
                'message' => __('accounting.pending_expenses_message', ['count' => $pendingExpensesCount]),
                'action_url' => route('modules.accounting.expenses.index', ['status' => 'pending']),
                'action_text' => __('accounting.review_expenses'),
            ];
        }

        // Draft invoices
        $draftInvoicesCount = Invoice::where('status', 'draft')->count();
        if ($draftInvoicesCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => __('accounting.draft_invoices'),
                'message' => __('accounting.draft_invoices_message', ['count' => $draftInvoicesCount]),
                'action_url' => route('modules.accounting.invoices.index', ['status' => 'draft']),
                'action_text' => __('accounting.complete_invoices'),
            ];
        }

        return $alerts;
    }

    /**
     * Get key performance indicators
     */
    public function getKPIs(): array
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $currentRevenue = Invoice::where('status', 'paid')
            ->where('invoice_date', '>=', $currentMonth)
            ->sum('total_amount');

        $lastMonthRevenue = Invoice::where('status', 'paid')
            ->whereBetween('invoice_date', [$lastMonth, $lastMonthEnd])
            ->sum('total_amount');

        $revenueGrowth = $lastMonthRevenue > 0
            ? (($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        $averageInvoiceValue = Invoice::where('status', 'paid')
            ->where('invoice_date', '>=', $currentMonth)
            ->avg('total_amount') ?? 0;

        $averagePaymentTime = $this->getAveragePaymentTime();

        return [
            'revenue_growth' => $revenueGrowth,
            'average_invoice_value' => $averageInvoiceValue,
            'average_payment_time' => $averagePaymentTime,
            'customer_retention_rate' => $this->getCustomerRetentionRate(),
            'profit_margin' => $this->getProfitMargin(),
        ];
    }

    /**
     * Get average payment time in days
     */
    private function getAveragePaymentTime(): float
    {
        $paidInvoices = Invoice::where('status', 'paid')
            ->whereNotNull('sent_at')
            ->where('invoice_date', '>=', now()->subMonths(3))
            ->get();

        if ($paidInvoices->isEmpty()) {
            return 0;
        }

        $totalDays = $paidInvoices->sum(function ($invoice) {
            return $invoice->sent_at->diffInDays($invoice->updated_at);
        });

        return $totalDays / $paidInvoices->count();
    }

    /**
     * Get customer retention rate
     */
    private function getCustomerRetentionRate(): float
    {
        $currentMonthCustomers = Customer::whereHas('invoices', function ($query) {
            $query->where('invoice_date', '>=', now()->startOfMonth());
        })->count();

        $lastMonthCustomers = Customer::whereHas('invoices', function ($query) {
            $query->whereBetween('invoice_date', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ]);
        })->count();

        return $lastMonthCustomers > 0
            ? ($currentMonthCustomers / $lastMonthCustomers) * 100
            : 0;
    }

    /**
     * Get overall profit margin
     */
    private function getProfitMargin(): float
    {
        $revenue = Invoice::where('status', 'paid')
            ->where('invoice_date', '>=', now()->startOfMonth())
            ->sum('total_amount');

        $expenses = Expense::where('status', 'paid')
            ->where('expense_date', '>=', now()->startOfMonth())
            ->sum('total_amount');

        return $revenue > 0 ? (($revenue - $expenses) / $revenue) * 100 : 0;
    }
}
