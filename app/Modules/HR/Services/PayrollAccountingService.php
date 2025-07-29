<?php

namespace App\Modules\HR\Services;

use App\Modules\HR\Models\SalaryRecord;
use App\Modules\Accounting\Models\JournalEntry;
use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\Expense;
use App\Modules\Accounting\Models\Vendor;
use Carbon\Carbon;

class PayrollAccountingService
{
    /**
     * Post salary record to accounting system.
     */
    public function postSalaryToAccounting(SalaryRecord $salaryRecord): void
    {
        if ($salaryRecord->is_posted_to_accounting) {
            return; // Already posted
        }

        // Create journal entry for salary expense
        $journalEntry = JournalEntry::create([
            'entry_number' => $this->generateEntryNumber(),
            'entry_date' => $salaryRecord->period_end,
            'description' => "Salary for {$salaryRecord->employee->display_name} - {$salaryRecord->period_text}",
            'reference' => $salaryRecord->payroll_number,
            'type' => 'automatic',
            'status' => 'posted',
        ]);

        // Get or create chart of accounts
        $salaryExpenseAccount = $this->getSalaryExpenseAccount();
        $socialInsurancePayableAccount = $this->getSocialInsurancePayableAccount();
        $incomeTaxPayableAccount = $this->getIncomeTaxPayableAccount();
        $salaryPayableAccount = $this->getSalaryPayableAccount();

        // Debit: Salary Expense (Gross Salary)
        $journalEntry->lines()->create([
            'account_id' => $salaryExpenseAccount->id,
            'description' => "Salary expense - {$salaryRecord->employee->display_name}",
            'debit' => $salaryRecord->gross_salary,
            'credit' => 0,
        ]);

        // Credit: Social Insurance Payable
        if ($salaryRecord->social_insurance > 0) {
            $journalEntry->lines()->create([
                'account_id' => $socialInsurancePayableAccount->id,
                'description' => "Social insurance deduction - {$salaryRecord->employee->display_name}",
                'debit' => 0,
                'credit' => $salaryRecord->social_insurance,
            ]);
        }

        // Credit: Income Tax Payable
        if ($salaryRecord->income_tax > 0) {
            $journalEntry->lines()->create([
                'account_id' => $incomeTaxPayableAccount->id,
                'description' => "Income tax deduction - {$salaryRecord->employee->display_name}",
                'debit' => 0,
                'credit' => $salaryRecord->income_tax,
            ]);
        }

        // Credit: Other deductions if any
        $otherDeductions = $salaryRecord->loan_deduction + $salaryRecord->advance_deduction + 
                          $salaryRecord->other_deductions + $salaryRecord->leave_deduction;
        
        if ($otherDeductions > 0) {
            $otherDeductionsAccount = $this->getOtherDeductionsAccount();
            $journalEntry->lines()->create([
                'account_id' => $otherDeductionsAccount->id,
                'description' => "Other deductions - {$salaryRecord->employee->display_name}",
                'debit' => 0,
                'credit' => $otherDeductions,
            ]);
        }

        // Credit: Salary Payable (Net Salary)
        $journalEntry->lines()->create([
            'account_id' => $salaryPayableAccount->id,
            'description' => "Net salary payable - {$salaryRecord->employee->display_name}",
            'debit' => 0,
            'credit' => $salaryRecord->net_salary,
        ]);

        // Update salary record
        $salaryRecord->update([
            'accounting_entry_id' => $journalEntry->id,
            'is_posted_to_accounting' => true,
        ]);

        // Create expense record for tracking
        $this->createExpenseRecord($salaryRecord);
    }

    /**
     * Post bulk salary records to accounting.
     */
    public function postBulkSalariesToAccounting(array $salaryRecordIds): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($salaryRecordIds as $id) {
            try {
                $salaryRecord = SalaryRecord::find($id);
                if ($salaryRecord && !$salaryRecord->is_posted_to_accounting) {
                    $this->postSalaryToAccounting($salaryRecord);
                    $results['success']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Record ID {$id}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Create expense record for salary.
     */
    private function createExpenseRecord(SalaryRecord $salaryRecord): void
    {
        // Get or create employee as vendor
        $vendor = $this->getEmployeeAsVendor($salaryRecord->employee);
        
        // Get salary expense account
        $expenseAccount = $this->getSalaryExpenseAccount();

        Expense::create([
            'expense_number' => $this->generateExpenseNumber(),
            'vendor_id' => $vendor->id,
            'expense_account_id' => $expenseAccount->id,
            'expense_date' => $salaryRecord->period_end,
            'amount' => $salaryRecord->gross_salary,
            'tax_amount' => 0,
            'total_amount' => $salaryRecord->gross_salary,
            'description' => "Salary payment - {$salaryRecord->employee->display_name} ({$salaryRecord->period_text})",
            'reference' => $salaryRecord->payroll_number,
            'status' => 'paid',
            'payment_method' => $salaryRecord->payment_method ?? 'bank_transfer',
            'payment_date' => $salaryRecord->payment_date,
            'notes' => "Auto-generated from payroll system",
        ]);
    }

    /**
     * Get or create employee as vendor.
     */
    private function getEmployeeAsVendor($employee): Vendor
    {
        return Vendor::firstOrCreate(
            ['email' => $employee->email],
            [
                'name' => $employee->display_name,
                'company' => 'Employee',
                'phone' => $employee->mobile ?? $employee->phone,
                'address' => $employee->full_address,
                'tax_number' => $employee->national_id,
                'payment_terms' => 'immediate',
                'is_active' => true,
                'notes' => 'Auto-created from HR system',
            ]
        );
    }

    /**
     * Get or create salary expense account.
     */
    private function getSalaryExpenseAccount(): ChartOfAccount
    {
        return ChartOfAccount::firstOrCreate(
            ['account_code' => '6100'],
            [
                'account_name' => 'Salary Expense',
                'account_name_ar' => 'مصروف الرواتب',
                'account_type' => 'expense',
                'parent_id' => null,
                'is_active' => true,
                'description' => 'Employee salary expenses',
            ]
        );
    }

    /**
     * Get or create social insurance payable account.
     */
    private function getSocialInsurancePayableAccount(): ChartOfAccount
    {
        return ChartOfAccount::firstOrCreate(
            ['account_code' => '2200'],
            [
                'account_name' => 'Social Insurance Payable',
                'account_name_ar' => 'التأمينات الاجتماعية المستحقة',
                'account_type' => 'liability',
                'parent_id' => null,
                'is_active' => true,
                'description' => 'Social insurance deductions payable',
            ]
        );
    }

    /**
     * Get or create income tax payable account.
     */
    private function getIncomeTaxPayableAccount(): ChartOfAccount
    {
        return ChartOfAccount::firstOrCreate(
            ['account_code' => '2210'],
            [
                'account_name' => 'Income Tax Payable',
                'account_name_ar' => 'ضريبة الدخل المستحقة',
                'account_type' => 'liability',
                'parent_id' => null,
                'is_active' => true,
                'description' => 'Income tax deductions payable',
            ]
        );
    }

    /**
     * Get or create salary payable account.
     */
    private function getSalaryPayableAccount(): ChartOfAccount
    {
        return ChartOfAccount::firstOrCreate(
            ['account_code' => '2100'],
            [
                'account_name' => 'Salary Payable',
                'account_name_ar' => 'الرواتب المستحقة',
                'account_type' => 'liability',
                'parent_id' => null,
                'is_active' => true,
                'description' => 'Net salary payable to employees',
            ]
        );
    }

    /**
     * Get or create other deductions account.
     */
    private function getOtherDeductionsAccount(): ChartOfAccount
    {
        return ChartOfAccount::firstOrCreate(
            ['account_code' => '2220'],
            [
                'account_name' => 'Other Deductions Payable',
                'account_name_ar' => 'استقطاعات أخرى مستحقة',
                'account_type' => 'liability',
                'parent_id' => null,
                'is_active' => true,
                'description' => 'Other employee deductions payable',
            ]
        );
    }

    /**
     * Generate journal entry number.
     */
    private function generateEntryNumber(): string
    {
        $lastEntry = JournalEntry::orderBy('entry_number', 'desc')->first();
        
        if ($lastEntry && preg_match('/JE(\d+)/', $lastEntry->entry_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1001;
        }

        return 'JE' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate expense number.
     */
    private function generateExpenseNumber(): string
    {
        $lastExpense = Expense::orderBy('expense_number', 'desc')->first();
        
        if ($lastExpense && preg_match('/EXP(\d+)/', $lastExpense->expense_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1001;
        }

        return 'EXP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get payroll summary for accounting.
     */
    public function getPayrollSummary(int $year, int $month): array
    {
        $salaryRecords = SalaryRecord::where('year', $year)
            ->where('month', $month)
            ->with('employee')
            ->get();

        return [
            'period' => Carbon::create($year, $month, 1)->format('F Y'),
            'total_employees' => $salaryRecords->count(),
            'total_gross_salary' => $salaryRecords->sum('gross_salary'),
            'total_deductions' => $salaryRecords->sum('total_deductions'),
            'total_net_salary' => $salaryRecords->sum('net_salary'),
            'total_social_insurance' => $salaryRecords->sum('social_insurance'),
            'total_income_tax' => $salaryRecords->sum('income_tax'),
            'posted_to_accounting' => $salaryRecords->where('is_posted_to_accounting', true)->count(),
            'pending_posting' => $salaryRecords->where('is_posted_to_accounting', false)->count(),
        ];
    }

    /**
     * Reverse accounting entry for salary record.
     */
    public function reverseSalaryAccounting(SalaryRecord $salaryRecord): void
    {
        if (!$salaryRecord->is_posted_to_accounting || !$salaryRecord->accounting_entry_id) {
            return;
        }

        $originalEntry = JournalEntry::find($salaryRecord->accounting_entry_id);
        if (!$originalEntry) {
            return;
        }

        // Create reversal entry
        $reversalEntry = JournalEntry::create([
            'entry_number' => $this->generateEntryNumber(),
            'entry_date' => now(),
            'description' => "Reversal of: {$originalEntry->description}",
            'reference' => "REV-{$originalEntry->entry_number}",
            'type' => 'reversal',
            'status' => 'posted',
        ]);

        // Reverse all lines (swap debit and credit)
        foreach ($originalEntry->lines as $line) {
            $reversalEntry->lines()->create([
                'account_id' => $line->account_id,
                'description' => "Reversal: {$line->description}",
                'debit' => $line->credit,
                'credit' => $line->debit,
            ]);
        }

        // Update salary record
        $salaryRecord->update([
            'is_posted_to_accounting' => false,
            'accounting_entry_id' => null,
        ]);
    }
}
