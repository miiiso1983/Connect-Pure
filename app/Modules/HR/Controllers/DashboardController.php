<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\LeaveRequest;
use App\Modules\HR\Models\Attendance;
use App\Modules\HR\Models\SalaryRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the HR dashboard.
     */
    public function index(): View
    {
        $dashboardData = $this->getDashboardData();
        
        return view('modules.hr.dashboard', compact('dashboardData'));
    }

    /**
     * Get dashboard data.
     */
    private function getDashboardData(): array
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now()->startOfMonth();
        $currentYear = Carbon::now()->year;

        // Employee Statistics
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::active()->count();
        $employeesOnProbation = Employee::active()
            ->where('probation_end_date', '>', $today)
            ->count();

        // Department Statistics
        $departmentsCount = Department::active()->count();
        $employeesByDepartment = Employee::active()
            ->with('department')
            ->get()
            ->groupBy('department.name')
            ->map->count()
            ->toArray();

        // Leave Statistics
        $pendingLeaveRequests = LeaveRequest::pending()->count();
        $employeesOnLeave = LeaveRequest::approved()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();

        $leaveRequestsByType = LeaveRequest::currentYear()
            ->approved()
            ->selectRaw('leave_type, COUNT(*) as count')
            ->groupBy('leave_type')
            ->pluck('count', 'leave_type')
            ->toArray();

        // Attendance Statistics for Today
        $attendanceToday = Attendance::where('date', $today)->get();
        $presentToday = $attendanceToday->whereIn('status', ['present', 'late'])->count();
        $lateToday = $attendanceToday->where('status', 'late')->count();
        $absentToday = $attendanceToday->where('status', 'absent')->count();

        // Monthly Attendance Rate
        $monthlyAttendance = Attendance::whereBetween('date', [
            $currentMonth,
            $currentMonth->copy()->endOfMonth()
        ])->get();

        $attendanceRate = $monthlyAttendance->count() > 0 
            ? ($monthlyAttendance->whereIn('status', ['present', 'late'])->count() / $monthlyAttendance->count()) * 100 
            : 0;

        // Salary Statistics
        $averageSalary = Employee::active()->avg('basic_salary') ?? 0;
        $totalSalaryExpense = Employee::active()->sum('basic_salary');
        
        $monthlyPayroll = SalaryRecord::where('year', $currentYear)
            ->where('month', Carbon::now()->month)
            ->sum('net_salary');

        // Recent Activities
        $recentHires = Employee::active()
            ->where('hire_date', '>=', $today->copy()->subDays(30))
            ->orderBy('hire_date', 'desc')
            ->limit(5)
            ->get();

        $upcomingBirthdays = Employee::active()
            ->whereRaw('DATE_FORMAT(date_of_birth, "%m-%d") BETWEEN ? AND ?', [
                $today->format('m-d'),
                $today->copy()->addDays(30)->format('m-d')
            ])
            ->orderByRaw('DATE_FORMAT(date_of_birth, "%m-%d")')
            ->limit(5)
            ->get();

        $recentLeaveRequests = LeaveRequest::with(['employee', 'approver'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Charts Data
        $monthlyHiresData = $this->getMonthlyHiresData();
        $departmentDistribution = $this->getDepartmentDistribution();
        $attendanceTrend = $this->getAttendanceTrend();

        return [
            'statistics' => [
                'total_employees' => $totalEmployees,
                'active_employees' => $activeEmployees,
                'departments_count' => $departmentsCount,
                'employees_on_probation' => $employeesOnProbation,
                'pending_leave_requests' => $pendingLeaveRequests,
                'employees_on_leave' => $employeesOnLeave,
                'present_today' => $presentToday,
                'late_today' => $lateToday,
                'absent_today' => $absentToday,
                'attendance_rate' => round($attendanceRate, 1),
                'average_salary' => $averageSalary,
                'total_salary_expense' => $totalSalaryExpense,
                'monthly_payroll' => $monthlyPayroll,
            ],
            'distributions' => [
                'employees_by_department' => $employeesByDepartment,
                'leave_requests_by_type' => $leaveRequestsByType,
                'department_distribution' => $departmentDistribution,
            ],
            'recent_activities' => [
                'recent_hires' => $recentHires,
                'upcoming_birthdays' => $upcomingBirthdays,
                'recent_leave_requests' => $recentLeaveRequests,
            ],
            'charts' => [
                'monthly_hires' => $monthlyHiresData,
                'attendance_trend' => $attendanceTrend,
            ],
        ];
    }

    /**
     * Get monthly hires data for the last 12 months.
     */
    private function getMonthlyHiresData(): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Employee::whereYear('hire_date', $date->year)
                ->whereMonth('hire_date', $date->month)
                ->count();
            
            $data[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }
        
        return $data;
    }

    /**
     * Get department distribution data.
     */
    private function getDepartmentDistribution(): array
    {
        return Department::withCount(['activeEmployees'])
            ->having('active_employees_count', '>', 0)
            ->get()
            ->map(function ($department) {
                return [
                    'name' => $department->display_name,
                    'count' => $department->active_employees_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get attendance trend for the last 30 days.
     */
    private function getAttendanceTrend(): array
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Skip weekends
            if (in_array($date->dayOfWeek, [5, 6])) { // Friday and Saturday
                continue;
            }
            
            $attendance = Attendance::where('date', $date->toDateString())->get();
            $total = $attendance->count();
            $present = $attendance->whereIn('status', ['present', 'late'])->count();
            $rate = $total > 0 ? ($present / $total) * 100 : 0;
            
            $data[] = [
                'date' => $date->format('M d'),
                'rate' => round($rate, 1),
                'present' => $present,
                'total' => $total,
            ];
        }
        
        return $data;
    }

    /**
     * Get quick stats for AJAX requests.
     */
    public function quickStats(Request $request)
    {
        $today = Carbon::today();
        
        $stats = [
            'employees_present' => Attendance::where('date', $today)
                ->whereIn('status', ['present', 'late'])
                ->count(),
            'employees_absent' => Attendance::where('date', $today)
                ->where('status', 'absent')
                ->count(),
            'pending_leave_requests' => LeaveRequest::pending()->count(),
            'employees_on_leave' => LeaveRequest::approved()
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get department performance data.
     */
    public function departmentPerformance(Request $request)
    {
        $departments = Department::withCount(['activeEmployees'])
            ->with(['activeEmployees' => function ($query) {
                $query->select('id', 'department_id', 'basic_salary');
            }])
            ->get()
            ->map(function ($department) {
                return [
                    'name' => $department->display_name,
                    'employee_count' => $department->active_employees_count,
                    'average_salary' => $department->activeEmployees->avg('basic_salary') ?? 0,
                    'total_salary' => $department->activeEmployees->sum('basic_salary'),
                    'budget_utilization' => $department->getBudgetUtilization(),
                ];
            });

        return response()->json($departments);
    }
}
