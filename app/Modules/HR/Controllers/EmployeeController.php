<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request): View
    {
        $query = Employee::with(['department', 'role', 'manager']);

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('department_id')) {
            $query->byDepartment($request->department_id);
        }

        if ($request->filled('role_id')) {
            $query->byRole($request->role_id);
        }

        if ($request->filled('employment_type')) {
            $query->byEmploymentType($request->employment_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $employees = $query->paginate(15)->withQueryString();

        // Get filter options
        $departments = Department::active()->orderBy('name')->get();
        $roles = Role::active()->orderBy('name')->get();

        return view('modules.hr.employees.index', compact(
            'employees',
            'departments',
            'roles'
        ));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        $departments = Department::active()->orderBy('name')->get();
        $roles = Role::active()->orderBy('name')->get();
        $managers = Employee::active()
            ->whereHas('role', function ($query) {
                $query->whereIn('level', ['lead', 'manager']);
            })
            ->orderBy('first_name')
            ->get();

        return view('modules.hr.employees.create', compact(
            'departments',
            'roles',
            'managers'
        ));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_ar' => 'nullable|string|max:255',
            'last_name_ar' => 'nullable|string|max:255',
            'email' => 'required|email|unique:hr_employees,email',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'nationality' => 'nullable|string|max:100',
            'national_id' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'department_id' => 'required|exists:hr_departments,id',
            'role_id' => 'required|exists:hr_roles,id',
            'manager_id' => 'nullable|exists:hr_employees,id',
            'hire_date' => 'required|date',
            'probation_end_date' => 'nullable|date|after:hire_date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Generate employee number
        $validated['employee_number'] = Employee::generateEmployeeNumber();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('employees/photos', 'public');
        }

        // Validate salary against role range
        $role = Role::find($validated['role_id']);
        $salaryValidation = $role->canAcceptSalary($validated['basic_salary']);
        if (!$salaryValidation['valid']) {
            return back()->withErrors(['basic_salary' => $salaryValidation['message']])
                        ->withInput();
        }

        $employee = Employee::create($validated);

        return redirect()->route('modules.hr.employees.show', $employee)
                        ->with('success', __('hr.employee_created_successfully'));
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): View
    {
        $employee->load([
            'department',
            'role',
            'manager',
            'subordinates',
            'leaveRequests' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
            'attendance' => function ($query) {
                $query->orderBy('date', 'desc')->limit(30);
            },
            'salaryRecords' => function ($query) {
                $query->orderBy('period_end', 'desc')->limit(12);
            }
        ]);

        // Get attendance summary for current month
        $attendanceSummary = $employee->getAttendanceForMonth(
            now()->year,
            now()->month
        );

        // Get leave balance summary
        $leaveBalance = [
            'annual' => $employee->annual_leave_balance,
            'sick' => $employee->sick_leave_balance,
            'emergency' => $employee->emergency_leave_balance,
        ];

        return view('modules.hr.employees.show', compact(
            'employee',
            'attendanceSummary',
            'leaveBalance'
        ));
    }

    /**
     * Show the form for editing the employee.
     */
    public function edit(Employee $employee): View
    {
        $departments = Department::active()->orderBy('name')->get();
        $roles = Role::active()->orderBy('name')->get();
        $managers = Employee::active()
            ->where('id', '!=', $employee->id)
            ->whereHas('role', function ($query) {
                $query->whereIn('level', ['lead', 'manager']);
            })
            ->orderBy('first_name')
            ->get();

        return view('modules.hr.employees.edit', compact(
            'employee',
            'departments',
            'roles',
            'managers'
        ));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_ar' => 'nullable|string|max:255',
            'last_name_ar' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('hr_employees')->ignore($employee->id)],
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'nationality' => 'nullable|string|max:100',
            'national_id' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'department_id' => 'required|exists:hr_departments,id',
            'role_id' => 'required|exists:hr_roles,id',
            'manager_id' => 'nullable|exists:hr_employees,id',
            'hire_date' => 'required|date',
            'probation_end_date' => 'nullable|date|after:hire_date',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'status' => 'required|in:active,inactive,terminated,resigned',
            'termination_date' => 'nullable|date|after:hire_date',
            'termination_reason' => 'nullable|string',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'iban' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|max:2048',
            'annual_leave_balance' => 'required|integer|min:0',
            'sick_leave_balance' => 'required|integer|min:0',
            'emergency_leave_balance' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($employee->profile_photo) {
                Storage::disk('public')->delete($employee->profile_photo);
            }
            
            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('employees/photos', 'public');
        }

        // Validate salary against role range
        $role = Role::find($validated['role_id']);
        $salaryValidation = $role->canAcceptSalary($validated['basic_salary']);
        if (!$salaryValidation['valid']) {
            return back()->withErrors(['basic_salary' => $salaryValidation['message']])
                        ->withInput();
        }

        $employee->update($validated);

        return redirect()->route('modules.hr.employees.show', $employee)
                        ->with('success', __('hr.employee_updated_successfully'));
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        // Check if employee has subordinates
        if ($employee->subordinates()->exists()) {
            return back()->with('error', 'Cannot delete employee with subordinates. Please reassign them first.');
        }

        // Check if employee has salary records
        if ($employee->salaryRecords()->exists()) {
            return back()->with('error', 'Cannot delete employee with salary records. Please archive instead.');
        }

        // Delete profile photo
        if ($employee->profile_photo) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        $employee->delete();

        return redirect()->route('modules.hr.employees.index')
                        ->with('success', __('hr.employee_deleted_successfully'));
    }

    /**
     * Get roles by department (AJAX).
     */
    public function getRolesByDepartment(Request $request)
    {
        $departmentId = $request->get('department_id');
        
        $roles = Role::active()
            ->where('department_id', $departmentId)
            ->orderBy('name')
            ->get(['id', 'name', 'name_ar', 'min_salary', 'max_salary']);

        return response()->json($roles);
    }

    /**
     * Export employees to Excel.
     */
    public function export(Request $request)
    {
        // This would implement Excel export functionality
        // For now, return a simple CSV
        
        $employees = Employee::with(['department', 'role'])->get();
        
        $filename = 'employees_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($employees) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Employee Number',
                'Full Name',
                'Email',
                'Department',
                'Role',
                'Hire Date',
                'Employment Type',
                'Status',
                'Basic Salary'
            ]);

            // CSV data
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->employee_number,
                    $employee->full_name,
                    $employee->email,
                    $employee->department->display_name,
                    $employee->role->display_name,
                    $employee->hire_date->format('Y-m-d'),
                    $employee->employment_type_text,
                    $employee->status,
                    $employee->basic_salary
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
