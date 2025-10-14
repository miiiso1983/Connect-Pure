<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request): View
    {
        $query = Role::withCount(['employees', 'activeEmployees'])
            ->with('department');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('department_id')) {
            $query->byDepartment($request->department_id);
        }

        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'vacant') {
                $query->whereDoesntHave('activeEmployees');
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $roles = $query->paginate(15)->withQueryString();

        // Get filter options
        $departments = Department::active()->orderBy('name')->get();

        return view('modules.hr.roles.index', compact('roles', 'departments'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        $departments = Department::active()->orderBy('name')->get();

        return view('modules.hr.roles.create', compact('departments'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'code' => 'required|string|max:20|unique:hr_roles,code',
            'department_id' => 'required|exists:hr_departments,id',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'level' => 'nullable|in:junior,mid,senior,lead,manager',
            'responsibilities' => 'nullable|array',
            'responsibilities.*' => 'string',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string',
            'is_active' => 'boolean',
        ]);

        $role = Role::create($validated);

        return redirect()->route('modules.hr.roles.show', $role)
            ->with('success', __('hr.role_created_successfully'));
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): View
    {
        $role->load([
            'department',
            'employees' => function ($query) {
                $query->with('department')->orderBy('first_name');
            },
        ]);

        // Get role statistics
        $stats = [
            'total_employees' => $role->employees->count(),
            'active_employees' => $role->employees->where('status', 'active')->count(),
            'average_salary' => $role->getAverageSalary(),
            'salary_budget' => $role->getSalaryBudget(),
            'is_vacant' => $role->is_vacant,
        ];

        return view('modules.hr.roles.show', compact('role', 'stats'));
    }

    /**
     * Show the form for editing the role.
     */
    public function edit(Role $role): View
    {
        $departments = Department::active()->orderBy('name')->get();

        return view('modules.hr.roles.edit', compact('role', 'departments'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'code' => ['required', 'string', 'max:20', Rule::unique('hr_roles')->ignore($role->id)],
            'department_id' => 'required|exists:hr_departments,id',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'level' => 'nullable|in:junior,mid,senior,lead,manager',
            'responsibilities' => 'nullable|array',
            'responsibilities.*' => 'string',
            'requirements' => 'nullable|array',
            'requirements.*' => 'string',
            'is_active' => 'boolean',
        ]);

        $role->update($validated);

        return redirect()->route('modules.hr.roles.show', $role)
            ->with('success', __('hr.role_updated_successfully'));
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Check if role has employees
        if ($role->employees()->exists()) {
            return back()->with('error', __('hr.cannot_delete_role_with_employees'));
        }

        $role->delete();

        return redirect()->route('modules.hr.roles.index')
            ->with('success', __('hr.role_deleted_successfully'));
    }

    /**
     * Toggle role status.
     */
    public function toggleStatus(Role $role): RedirectResponse
    {
        $role->update(['is_active' => ! $role->is_active]);

        $status = $role->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Role has been {$status} successfully.");
    }

    /**
     * Get roles by department (AJAX).
     */
    public function getByDepartment(Department $department)
    {
        $roles = $department->roles()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'name_ar', 'min_salary', 'max_salary', 'level']);

        return response()->json($roles);
    }

    /**
     * Get vacant roles (AJAX).
     */
    public function getVacantRoles()
    {
        $vacantRoles = Role::getVacantRoles();

        return response()->json($vacantRoles);
    }

    /**
     * Export roles to CSV.
     */
    public function export(Request $request)
    {
        $query = Role::withCount(['employees', 'activeEmployees'])
            ->with('department');

        // Apply same filters as index
        if ($request->filled('department_id')) {
            $query->byDepartment($request->department_id);
        }

        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        $roles = $query->get();

        $filename = 'roles_'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($roles) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Role Code',
                'Role Name',
                'Department',
                'Level',
                'Min Salary',
                'Max Salary',
                'Total Employees',
                'Active Employees',
                'Average Salary',
                'Status',
            ]);

            // CSV data
            foreach ($roles as $role) {
                fputcsv($file, [
                    $role->code,
                    $role->display_name,
                    $role->department->display_name,
                    $role->level_text,
                    $role->min_salary ?? 'N/A',
                    $role->max_salary ?? 'N/A',
                    $role->employees_count,
                    $role->active_employees_count,
                    number_format($role->getAverageSalary(), 2),
                    $role->is_active ? 'Active' : 'Inactive',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
