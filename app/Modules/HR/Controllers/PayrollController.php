<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\SalaryRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = $request->get('month', now()->month);
        $currentYear = $request->get('year', now()->year);

        $query = SalaryRecord::with(['employee.department'])
            ->byPeriod($currentYear, $currentMonth);

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrollRecords = $query->orderBy('created_at', 'desc')->paginate(20);

        // Summary statistics
        $summary = [
            'total_employees' => Employee::active()->count(),
            'processed_payroll' => $query->where('status', 'approved')->count(),
            'pending_approval' => $query->where('status', 'pending')->count(),
            'total_amount' => $query->where('status', 'approved')->sum('net_salary'),
        ];

        $departments = \App\Modules\HR\Models\Department::active()->get();
        $months = collect(range(1, 12))->map(function ($month) {
            return [
                'value' => $month,
                'label' => Carbon::create()->month($month)->format('F'),
            ];
        });

        $years = collect(range(now()->year - 2, now()->year + 1));

        return view('modules.hr.payroll.index', compact(
            'payrollRecords', 'summary', 'departments', 'months', 'years',
            'currentMonth', 'currentYear'
        ));
    }

    public function create(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Check if payroll already exists for this period
        $existingPayroll = SalaryRecord::byPeriod($year, $month)->exists();

        if ($existingPayroll) {
            return redirect()->route('modules.hr.payroll.index')
                ->with('error', __('hr.payroll_already_exists_for_period'));
        }

        $employees = Employee::active()->with(['department', 'attendanceRecords', 'leaveRequests'])->get();

        // Calculate payroll data for each employee
        $payrollData = $employees->map(function ($employee) use ($month, $year) {
            return $this->calculateEmployeePayroll($employee, $month, $year);
        });

        return view('modules.hr.payroll.create', compact('payrollData', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
            'payroll_data' => 'required|array',
            'payroll_data.*.employee_id' => 'required|exists:hr_employees,id',
            'payroll_data.*.basic_salary' => 'required|numeric|min:0',
            'payroll_data.*.allowances' => 'nullable|numeric|min:0',
            'payroll_data.*.deductions' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->payroll_data as $data) {
                $employee = Employee::findOrFail($data['employee_id']);

                SalaryRecord::create([
                    'employee_id' => $employee->id,
                    'period_start' => Carbon::create($request->year, $request->month, 1),
                    'period_end' => Carbon::create($request->year, $request->month, 1)->endOfMonth(),
                    'basic_salary' => $data['basic_salary'],
                    'allowances' => $data['allowances'] ?? 0,
                    'overtime_amount' => $data['overtime_amount'] ?? 0,
                    'deductions' => $data['deductions'] ?? 0,
                    'tax_deduction' => $data['tax_deduction'] ?? 0,
                    'net_salary' => $data['net_salary'],
                    'working_days' => $data['working_days'] ?? 22,
                    'worked_days' => $data['worked_days'] ?? 22,
                    'leave_days' => $data['leave_days'] ?? 0,
                    'overtime_hours' => $data['overtime_hours'] ?? 0,
                    'status' => 'pending',
                    'prepared_by' => auth()->id(),
                    'notes' => $data['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('modules.hr.payroll.index')
                ->with('success', __('hr.payroll_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput()
                ->withErrors(['error' => __('hr.error_creating_payroll')]);
        }
    }

    public function show(SalaryRecord $payroll)
    {
        $payroll->load(['employee.department', 'preparedBy', 'approvedBy']);

        return view('modules.hr.payroll.show', compact('payroll'));
    }

    public function approve(SalaryRecord $payroll)
    {
        if ($payroll->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => __('hr.payroll_already_processed'),
            ]);
        }

        $payroll->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('hr.payroll_approved_successfully'),
        ]);
    }

    public function reject(Request $request, SalaryRecord $payroll)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($payroll->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => __('hr.payroll_already_processed'),
            ]);
        }

        $payroll->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('hr.payroll_rejected_successfully'),
        ]);
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'payroll_ids' => 'required|array',
            'payroll_ids.*' => 'exists:hr_salary_records,id',
        ]);

        $updated = SalaryRecord::whereIn('id', $request->payroll_ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => __('hr.bulk_payroll_approved', ['count' => $updated]),
        ]);
    }

    public function generatePayslips(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:hr_employees,id',
        ]);

        $query = SalaryRecord::with(['employee.department'])
            ->byPeriod($request->year, $request->month)
            ->where('status', 'approved');

        if ($request->filled('employee_ids')) {
            $query->whereIn('employee_id', $request->employee_ids);
        }

        $payrollRecords = $query->get();

        if ($payrollRecords->isEmpty()) {
            return back()->with('error', __('hr.no_approved_payroll_found'));
        }

        // Generate PDF payslips
        $pdf = \PDF::loadView('modules.hr.payroll.payslips', compact('payrollRecords'));

        $filename = "payslips_{$request->year}_{$request->month}.pdf";

        return $pdf->download($filename);
    }

    public function reports(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $payrollSummary = SalaryRecord::byPeriod($year, $month)
            ->where('status', 'approved')
            ->selectRaw('
                                        COUNT(*) as total_employees,
                                        SUM(basic_salary) as total_basic_salary,
                                        SUM(allowances) as total_allowances,
                                        SUM(overtime_amount) as total_overtime,
                                        SUM(deductions) as total_deductions,
                                        SUM(tax_deduction) as total_tax,
                                        SUM(net_salary) as total_net_salary
                                    ')
            ->first();

        $departmentSummary = SalaryRecord::byPeriod($year, $month)
            ->where('status', 'approved')
            ->join('hr_employees', 'hr_salary_records.employee_id', '=', 'hr_employees.id')
            ->join('hr_departments', 'hr_employees.department_id', '=', 'hr_departments.id')
            ->selectRaw('
                                           hr_departments.name as department_name,
                                           COUNT(*) as employee_count,
                                           SUM(net_salary) as total_amount
                                       ')
            ->groupBy('hr_departments.id', 'hr_departments.name')
            ->get();

        return view('modules.hr.payroll.reports', compact(
            'payrollSummary', 'departmentSummary', 'month', 'year'
        ));
    }

    private function calculateEmployeePayroll(Employee $employee, int $month, int $year): array
    {
        $periodStart = Carbon::create($year, $month, 1);
        $periodEnd = $periodStart->copy()->endOfMonth();

        // Calculate working days (excluding weekends)
        $workingDays = 0;
        $current = $periodStart->copy();
        while ($current <= $periodEnd) {
            if (! $current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        // Get attendance records for the period
        $attendanceRecords = $employee->attendanceRecords()
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get();

        $workedDays = $attendanceRecords->where('status', 'present')->count();
        $overtimeHours = $attendanceRecords->sum('overtime_hours');

        // Get approved leave days
        $leaveDays = $employee->leaveRequests()
            ->where('status', 'approved')
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->whereBetween('start_date', [$periodStart, $periodEnd])
                    ->orWhereBetween('end_date', [$periodStart, $periodEnd])
                    ->orWhere(function ($q) use ($periodStart, $periodEnd) {
                        $q->where('start_date', '<=', $periodStart)
                            ->where('end_date', '>=', $periodEnd);
                    });
            })
            ->sum('days');

        // Calculate salary components
        $basicSalary = $employee->salary;
        $dailySalary = $basicSalary / $workingDays;
        $adjustedBasicSalary = $dailySalary * ($workedDays + $leaveDays);

        $allowances = 0; // Can be customized based on employee allowances
        $overtimeRate = ($basicSalary / $workingDays / 8) * 1.5; // 1.5x hourly rate
        $overtimeAmount = $overtimeHours * $overtimeRate;

        $grossSalary = $adjustedBasicSalary + $allowances + $overtimeAmount;

        // Calculate deductions
        $taxDeduction = $this->calculateTax($grossSalary);
        $otherDeductions = 0; // Can be customized
        $totalDeductions = $taxDeduction + $otherDeductions;

        $netSalary = $grossSalary - $totalDeductions;

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->full_name,
            'department' => $employee->department->name ?? 'N/A',
            'basic_salary' => $basicSalary,
            'adjusted_basic_salary' => $adjustedBasicSalary,
            'allowances' => $allowances,
            'overtime_hours' => $overtimeHours,
            'overtime_amount' => $overtimeAmount,
            'gross_salary' => $grossSalary,
            'tax_deduction' => $taxDeduction,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'working_days' => $workingDays,
            'worked_days' => $workedDays,
            'leave_days' => $leaveDays,
        ];
    }

    private function calculateTax(float $grossSalary): float
    {
        // Simple tax calculation - can be customized based on tax brackets
        if ($grossSalary <= 5000) {
            return 0;
        } elseif ($grossSalary <= 10000) {
            return ($grossSalary - 5000) * 0.1;
        } elseif ($grossSalary <= 20000) {
            return 500 + (($grossSalary - 10000) * 0.15);
        } else {
            return 2000 + (($grossSalary - 20000) * 0.2);
        }
    }
}
