<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\SalaryRecord;
use App\Modules\HR\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class SalaryRecordController extends Controller
{
    /**
     * Display a listing of salary records.
     */
    public function index(Request $request): View
    {
        $query = SalaryRecord::with(['employee.department', 'preparedBy', 'approvedBy']);

        // Apply filters
        if ($request->filled('search')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->search($request->search);
            });
        }

        if ($request->filled('employee_id')) {
            $query->byEmployee($request->employee_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('year')) {
            $query->byPeriod($request->year);
        }

        if ($request->filled('month')) {
            $query->byPeriod($request->year ?? now()->year, $request->month);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'period_end');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $salaryRecords = $query->paginate(15)->withQueryString();

        // Get filter options
        $employees = Employee::active()->orderBy('first_name')->get();

        // Get payroll statistics
        $stats = SalaryRecord::getPayrollStats(
            $request->year ?? now()->year,
            $request->month
        );

        return view('modules.hr.payroll.index', compact(
            'salaryRecords',
            'employees',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new salary record.
     */
    public function create(): View
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        $currentMonth = now();

        return view('modules.hr.payroll.create', compact('employees', 'currentMonth'));
    }

    /**
     * Store a newly created salary record.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'working_days' => 'required|integer|min:1|max:31',
            'actual_working_days' => 'required|integer|min:0|max:31',
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'communication_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|integer|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'social_insurance' => 'nullable|numeric|min:0',
            'income_tax' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'advance_deduction' => 'nullable|numeric|min:0',
            'absence_deduction' => 'nullable|numeric|min:0',
            'late_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'leave_days_taken' => 'nullable|integer|min:0',
            'leave_deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Check if record already exists
        $existing = SalaryRecord::where('employee_id', $validated['employee_id'])
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->first();

        if ($existing) {
            return back()->withErrors(['month' => 'Salary record already exists for this period.'])
                        ->withInput();
        }

        // Generate payroll number
        $validated['payroll_number'] = SalaryRecord::generatePayrollNumber();

        // Set period dates
        $validated['period_start'] = Carbon::create($validated['year'], $validated['month'], 1);
        $validated['period_end'] = $validated['period_start']->copy()->endOfMonth();

        // Set prepared by (for demo purposes, use first employee)
        $validated['prepared_by'] = Employee::first()->id;

        // Create salary record
        $salaryRecord = SalaryRecord::create($validated);

        // Calculate totals
        $salaryRecord->calculateNetSalary();

        return redirect()->route('modules.hr.payroll.show', $salaryRecord)
                        ->with('success', __('hr.salary_record_created_successfully'));
    }

    /**
     * Display the specified salary record.
     */
    public function show(SalaryRecord $salaryRecord): View
    {
        $salaryRecord->load([
            'employee.department',
            'employee.role',
            'preparedBy',
            'approvedBy',
            'accountingEntry'
        ]);

        return view('modules.hr.payroll.show', compact('salaryRecord'));
    }

    /**
     * Show the form for editing the salary record.
     */
    public function edit(SalaryRecord $salaryRecord): View
    {
        // Only allow editing draft records
        if ($salaryRecord->status !== 'draft') {
            abort(403, 'Cannot edit non-draft salary record.');
        }

        $employees = Employee::active()->orderBy('first_name')->get();

        return view('modules.hr.payroll.edit', compact('salaryRecord', 'employees'));
    }

    /**
     * Update the specified salary record.
     */
    public function update(Request $request, SalaryRecord $salaryRecord): RedirectResponse
    {
        // Only allow updating draft records
        if ($salaryRecord->status !== 'draft') {
            return back()->with('error', 'Cannot update non-draft salary record.');
        }

        $validated = $request->validate([
            'working_days' => 'required|integer|min:1|max:31',
            'actual_working_days' => 'required|integer|min:0|max:31',
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'communication_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|integer|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0',
            'social_insurance' => 'nullable|numeric|min:0',
            'income_tax' => 'nullable|numeric|min:0',
            'loan_deduction' => 'nullable|numeric|min:0',
            'advance_deduction' => 'nullable|numeric|min:0',
            'absence_deduction' => 'nullable|numeric|min:0',
            'late_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'leave_days_taken' => 'nullable|integer|min:0',
            'leave_deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $salaryRecord->update($validated);

        // Recalculate totals
        $salaryRecord->calculateNetSalary();

        return redirect()->route('modules.hr.payroll.show', $salaryRecord)
                        ->with('success', 'Salary record updated successfully.');
    }

    /**
     * Remove the specified salary record.
     */
    public function destroy(SalaryRecord $salaryRecord): RedirectResponse
    {
        // Only allow deleting draft records
        if ($salaryRecord->status !== 'draft') {
            return back()->with('error', 'Cannot delete non-draft salary record.');
        }

        $salaryRecord->delete();

        return redirect()->route('modules.hr.payroll.index')
                        ->with('success', 'Salary record deleted successfully.');
    }

    /**
     * Approve the salary record.
     */
    public function approve(SalaryRecord $salaryRecord): RedirectResponse
    {
        if (!$salaryRecord->can_be_approved) {
            return back()->with('error', 'Cannot approve this salary record.');
        }

        // For demo purposes, use the first manager
        $approverId = Employee::whereHas('role', function ($query) {
            $query->whereIn('level', ['manager', 'lead']);
        })->first()->id ?? 1;

        $salaryRecord->approve($approverId);

        return back()->with('success', __('hr.salary_record_approved_successfully'));
    }

    /**
     * Mark salary record as paid.
     */
    public function markAsPaid(Request $request, SalaryRecord $salaryRecord): RedirectResponse
    {
        if (!$salaryRecord->can_be_paid) {
            return back()->with('error', 'Cannot mark this salary record as paid.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash,check',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $salaryRecord->markAsPaid(
            $validated['payment_method'],
            $validated['payment_reference'] ?? null
        );

        return back()->with('success', 'Salary record marked as paid successfully.');
    }

    /**
     * Cancel the salary record.
     */
    public function cancel(SalaryRecord $salaryRecord): RedirectResponse
    {
        $salaryRecord->cancel();

        return back()->with('success', 'Salary record cancelled successfully.');
    }

    /**
     * Generate payslip view.
     */
    public function generatePayslip(SalaryRecord $salaryRecord): View
    {
        $payslipData = $salaryRecord->generatePayslip();

        return view('modules.hr.payroll.payslip', compact('salaryRecord', 'payslipData'));
    }

    /**
     * Download payslip as PDF.
     */
    public function downloadPayslip(SalaryRecord $salaryRecord)
    {
        // This would implement PDF generation
        // For now, return the payslip view
        return $this->generatePayslip($salaryRecord);
    }

    /**
     * Generate monthly salary records for all employees.
     */
    public function generateMonthly(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $employees = Employee::active()->get();
        $created = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // Check if record already exists
            if ($employee->hasActiveSalaryRecord($validated['year'], $validated['month'])) {
                $skipped++;
                continue;
            }

            // Create salary record
            $periodStart = Carbon::create($validated['year'], $validated['month'], 1);
            $periodEnd = $periodStart->copy()->endOfMonth();

            $salaryRecord = SalaryRecord::create([
                'payroll_number' => SalaryRecord::generatePayrollNumber(),
                'employee_id' => $employee->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'year' => $validated['year'],
                'month' => $validated['month'],
                'working_days' => $periodEnd->day,
                'actual_working_days' => $periodEnd->day, // Default to full month
                'basic_salary' => $employee->basic_salary,
                'prepared_by' => Employee::first()->id, // Demo
            ]);

            $salaryRecord->calculateNetSalary();
            $created++;
        }

        return back()->with('success', "Generated {$created} salary records. Skipped {$skipped} existing records.");
    }

    /**
     * Post salary record to accounting.
     */
    public function postToAccounting(SalaryRecord $salaryRecord): RedirectResponse
    {
        if ($salaryRecord->is_posted_to_accounting) {
            return back()->with('error', 'Already posted to accounting.');
        }

        try {
            $salaryRecord->postToAccounting();
            return back()->with('success', 'Posted to accounting successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to post to accounting: ' . $e->getMessage());
        }
    }

    /**
     * Bulk post salary records to accounting.
     */
    public function bulkPostToAccounting(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'salary_record_ids' => 'required|array',
            'salary_record_ids.*' => 'exists:hr_salary_records,id',
        ]);

        $service = new \App\Modules\HR\Services\PayrollAccountingService();
        $results = $service->postBulkSalariesToAccounting($validated['salary_record_ids']);

        $message = "Posted {$results['success']} records to accounting.";
        if ($results['failed'] > 0) {
            $message .= " {$results['failed']} failed.";
        }

        return back()->with('success', $message);
    }

    /**
     * Export salary records to CSV.
     */
    public function export(Request $request)
    {
        $query = SalaryRecord::with(['employee.department']);

        // Apply same filters as index
        if ($request->filled('employee_id')) {
            $query->byEmployee($request->employee_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('year')) {
            $query->byPeriod($request->year);
        }

        $salaryRecords = $query->get();
        
        $filename = 'salary_records_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($salaryRecords) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Payroll Number',
                'Employee',
                'Department',
                'Period',
                'Basic Salary',
                'Gross Salary',
                'Total Deductions',
                'Net Salary',
                'Status',
                'Payment Date'
            ]);

            // CSV data
            foreach ($salaryRecords as $record) {
                fputcsv($file, [
                    $record->payroll_number,
                    $record->employee->display_name,
                    $record->employee->department->display_name,
                    $record->period_text,
                    $record->basic_salary,
                    $record->gross_salary,
                    $record->total_deductions,
                    $record->net_salary,
                    $record->status_text,
                    $record->payment_date ? $record->payment_date->format('Y-m-d') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
