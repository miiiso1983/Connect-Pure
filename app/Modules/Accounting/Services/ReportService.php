<?php

namespace App\Modules\Accounting\Services;

use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\Expense;
use App\Modules\Accounting\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Generate Profit & Loss Report
     */
    public function getProfitLossReport(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Revenue Accounts
        $revenueAccounts = ChartOfAccount::byType('revenue')->active()->get();
        $revenues = $this->getAccountBalances($revenueAccounts, $start, $end);
        $totalRevenue = $revenues->sum('balance');

        // Expense Accounts
        $expenseAccounts = ChartOfAccount::byType('expense')->active()->get();
        $expenses = $this->getAccountBalances($expenseAccounts, $start, $end);
        $totalExpenses = $expenses->sum('balance');

        // Calculate totals
        $grossProfit = $totalRevenue;
        $netIncome = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($netIncome / $totalRevenue) * 100 : 0;

        return [
            'period' => [
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'period_text' => $start->format('M d, Y').' - '.$end->format('M d, Y'),
            ],
            'revenue' => [
                'accounts' => $revenues,
                'total' => $totalRevenue,
            ],
            'expenses' => [
                'accounts' => $expenses,
                'total' => $totalExpenses,
            ],
            'summary' => [
                'gross_profit' => $grossProfit,
                'net_income' => $netIncome,
                'profit_margin' => $profitMargin,
            ],
        ];
    }

    /**
     * Generate Balance Sheet Report
     */
    public function getBalanceSheetReport(string $asOfDate): array
    {
        $date = Carbon::parse($asOfDate);

        // Assets
        $assetAccounts = ChartOfAccount::byType('asset')->active()->get();
        $assets = $this->getAccountBalances($assetAccounts, null, $date);
        $totalAssets = $assets->sum('balance');

        // Liabilities
        $liabilityAccounts = ChartOfAccount::byType('liability')->active()->get();
        $liabilities = $this->getAccountBalances($liabilityAccounts, null, $date);
        $totalLiabilities = $liabilities->sum('balance');

        // Equity
        $equityAccounts = ChartOfAccount::byType('equity')->active()->get();
        $equity = $this->getAccountBalances($equityAccounts, null, $date);
        $totalEquity = $equity->sum('balance');

        // Calculate retained earnings (simplified)
        $retainedEarnings = $this->getRetainedEarnings($date);
        $totalEquityWithRetained = $totalEquity + $retainedEarnings;

        return [
            'as_of_date' => $date->format('Y-m-d'),
            'as_of_text' => $date->format('M d, Y'),
            'assets' => [
                'accounts' => $assets,
                'total' => $totalAssets,
            ],
            'liabilities' => [
                'accounts' => $liabilities,
                'total' => $totalLiabilities,
            ],
            'equity' => [
                'accounts' => $equity,
                'retained_earnings' => $retainedEarnings,
                'total' => $totalEquityWithRetained,
            ],
            'totals' => [
                'total_assets' => $totalAssets,
                'total_liabilities_equity' => $totalLiabilities + $totalEquityWithRetained,
                'balanced' => abs($totalAssets - ($totalLiabilities + $totalEquityWithRetained)) < 0.01,
            ],
        ];
    }

    /**
     * Generate Cash Flow Report
     */
    public function getCashFlowReport(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Operating Activities
        $operatingCashFlow = $this->getOperatingCashFlow($start, $end);

        // Investing Activities (simplified)
        $investingCashFlow = $this->getInvestingCashFlow($start, $end);

        // Financing Activities (simplified)
        $financingCashFlow = $this->getFinancingCashFlow($start, $end);

        $netCashFlow = $operatingCashFlow['total'] + $investingCashFlow['total'] + $financingCashFlow['total'];

        return [
            'period' => [
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'period_text' => $start->format('M d, Y').' - '.$end->format('M d, Y'),
            ],
            'operating_activities' => $operatingCashFlow,
            'investing_activities' => $investingCashFlow,
            'financing_activities' => $financingCashFlow,
            'net_cash_flow' => $netCashFlow,
        ];
    }

    /**
     * Generate Trial Balance Report
     */
    public function getTrialBalanceReport(string $asOfDate): array
    {
        $date = Carbon::parse($asOfDate);
        $accounts = ChartOfAccount::active()->orderBy('account_code')->get();

        $trialBalance = $accounts->map(function ($account) use ($date) {
            $balance = $account->getBalance(null, $date->format('Y-m-d'));

            return [
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'debit_balance' => $account->isDebitAccount() && $balance > 0 ? $balance : 0,
                'credit_balance' => $account->isCreditAccount() && $balance > 0 ? $balance : 0,
                'balance' => $balance,
            ];
        });

        $totalDebits = $trialBalance->sum('debit_balance');
        $totalCredits = $trialBalance->sum('credit_balance');

        return [
            'as_of_date' => $date->format('Y-m-d'),
            'as_of_text' => $date->format('M d, Y'),
            'accounts' => $trialBalance,
            'totals' => [
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits,
                'difference' => $totalDebits - $totalCredits,
                'balanced' => abs($totalDebits - $totalCredits) < 0.01,
            ],
        ];
    }

    /**
     * Get account balances for a period
     */
    private function getAccountBalances(Collection $accounts, ?Carbon $startDate, Carbon $endDate): Collection
    {
        return $accounts->map(function ($account) use ($startDate, $endDate) {
            $balance = $account->getBalance(
                $startDate?->format('Y-m-d'),
                $endDate->format('Y-m-d')
            );

            return [
                'account_id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'balance' => abs($balance), // Use absolute value for reporting
                'formatted_balance' => number_format(abs($balance), 2),
            ];
        })->filter(function ($account) {
            return $account['balance'] > 0; // Only show accounts with balances
        });
    }

    /**
     * Calculate retained earnings
     */
    private function getRetainedEarnings(Carbon $date): float
    {
        // Simplified calculation: Total revenue - Total expenses up to date
        $totalRevenue = Invoice::where('status', 'paid')
            ->where('invoice_date', '<=', $date)
            ->sum('total_amount');

        $totalExpenses = Expense::where('status', 'paid')
            ->where('expense_date', '<=', $date)
            ->sum('total_amount');

        return $totalRevenue - $totalExpenses;
    }

    /**
     * Get operating cash flow
     */
    private function getOperatingCashFlow(Carbon $start, Carbon $end): array
    {
        $cashFromCustomers = Invoice::where('status', 'paid')
            ->whereBetween('invoice_date', [$start, $end])
            ->sum('paid_amount');

        $cashToSuppliers = Expense::where('status', 'paid')
            ->whereBetween('expense_date', [$start, $end])
            ->sum('total_amount');

        return [
            'cash_from_customers' => $cashFromCustomers,
            'cash_to_suppliers' => -$cashToSuppliers,
            'total' => $cashFromCustomers - $cashToSuppliers,
            'items' => [
                [
                    'description' => __('accounting.cash_received_from_customers'),
                    'amount' => $cashFromCustomers,
                ],
                [
                    'description' => __('accounting.cash_paid_to_suppliers'),
                    'amount' => -$cashToSuppliers,
                ],
            ],
        ];
    }

    /**
     * Get investing cash flow (simplified)
     */
    private function getInvestingCashFlow(Carbon $start, Carbon $end): array
    {
        // This would include purchases/sales of fixed assets, investments, etc.
        // For now, we'll return a simplified structure
        return [
            'total' => 0,
            'items' => [],
        ];
    }

    /**
     * Get financing cash flow (simplified)
     */
    private function getFinancingCashFlow(Carbon $start, Carbon $end): array
    {
        // This would include loans, equity transactions, dividends, etc.
        // For now, we'll return a simplified structure
        return [
            'total' => 0,
            'items' => [],
        ];
    }

    /**
     * Export report to PDF
     */
    public function exportToPDF(string $reportType, ?string $startDate = null, ?string $endDate = null): string
    {
        $reportData = match ($reportType) {
            'profit_loss' => $this->getProfitLossReport($startDate, $endDate),
            'balance_sheet' => $this->getBalanceSheetReport($endDate ?? now()->format('Y-m-d')),
            'cash_flow' => $this->getCashFlowReport($startDate, $endDate),
            'trial_balance' => $this->getTrialBalanceReport($endDate ?? now()->format('Y-m-d')),
            default => []
        };

        // Here you would use a PDF library like DomPDF or wkhtmltopdf
        // For now, we'll return a placeholder
        return "PDF content for {$reportType} report";
    }

    /**
     * Get aging report for customers
     */
    public function getCustomerAgingReport(): array
    {
        $customers = \App\Modules\Accounting\Models\Customer::with(['invoices' => function ($query) {
            $query->whereIn('status', ['sent', 'viewed', 'partial', 'overdue'])
                ->where('balance_due', '>', 0);
        }])->get();

        $agingData = $customers->map(function ($customer) {
            $invoices = $customer->invoices;

            $current = $invoices->where('due_date', '>=', now())->sum('balance_due');
            $days30 = $invoices->where('due_date', '<', now())
                ->where('due_date', '>=', now()->subDays(30))->sum('balance_due');
            $days60 = $invoices->where('due_date', '<', now()->subDays(30))
                ->where('due_date', '>=', now()->subDays(60))->sum('balance_due');
            $days90 = $invoices->where('due_date', '<', now()->subDays(60))
                ->where('due_date', '>=', now()->subDays(90))->sum('balance_due');
            $over90 = $invoices->where('due_date', '<', now()->subDays(90))->sum('balance_due');

            return [
                'customer_name' => $customer->display_name,
                'current' => $current,
                '1_30_days' => $days30,
                '31_60_days' => $days60,
                '61_90_days' => $days90,
                'over_90_days' => $over90,
                'total' => $current + $days30 + $days60 + $days90 + $over90,
            ];
        })->filter(function ($customer) {
            return $customer['total'] > 0;
        });

        return [
            'customers' => $agingData,
            'totals' => [
                'current' => $agingData->sum('current'),
                '1_30_days' => $agingData->sum('1_30_days'),
                '31_60_days' => $agingData->sum('31_60_days'),
                '61_90_days' => $agingData->sum('61_90_days'),
                'over_90_days' => $agingData->sum('over_90_days'),
                'total' => $agingData->sum('total'),
            ],
        ];
    }
}
