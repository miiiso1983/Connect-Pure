<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\LeaveRequest;
use App\Modules\HR\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRController extends Controller
{
    public function index()
    {
        // Get HR statistics
        $stats = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'on_leave' => LeaveRequest::where('status', 'approved')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
            'new_hires' => Employee::where('hire_date', '>=', now()->subDays(30))->count(),
            'departments' => Department::count(),
            'pending_leave_requests' => LeaveRequest::where('status', 'pending')->count(),
        ];

        // Get recent activities
        $recentHires = Employee::with(['department', 'role'])
            ->where('hire_date', '>=', now()->subDays(30))
            ->orderBy('hire_date', 'desc')
            ->take(5)
            ->get();

        $pendingLeaveRequests = LeaveRequest::with('employee')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get department breakdown
        $departmentStats = Department::withCount('employees')->get();

        return view('modules.hr.index', compact(
            'stats',
            'recentHires',
            'pendingLeaveRequests',
            'departmentStats'
        ));
    }

    public function dashboard()
    {
        return view('modules.hr.dashboard');
    }

    public function employees()
    {
        $employees = Employee::with(['department', 'role', 'manager'])
            ->paginate(15);

        return view('modules.hr.employees.index', compact('employees'));
    }

    public function createEmployee()
    {
        $departments = Department::all();
        $managers = Employee::where('status', 'active')->get();

        return view('modules.hr.employees.create', compact('departments', 'managers'));
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'employee_number' => 'required|unique:hr_employees',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:hr_employees',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'required|exists:hr_departments,id',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        Employee::create($validated);

        return redirect()->route('modules.hr.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function showEmployee(Employee $employee)
    {
        $employee->load(['department', 'role', 'manager', 'leaveRequests', 'attendanceRecords']);

        return view('modules.hr.employees.show', compact('employee'));
    }

    public function editEmployee(Employee $employee)
    {
        $departments = Department::all();
        $managers = Employee::where('status', 'active')->where('id', '!=', $employee->id)->get();

        return view('modules.hr.employees.edit', compact('employee', 'departments', 'managers'));
    }

    public function updateEmployee(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_number' => 'required|unique:hr_employees,employee_number,' . $employee->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:hr_employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'department_id' => 'required|exists:hr_departments,id',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        $employee->update($validated);

        return redirect()->route('modules.hr.employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroyEmployee(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('modules.hr.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
