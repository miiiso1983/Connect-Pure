<?php

namespace App\Modules\HR\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HR\Models\Department;
use App\Modules\HR\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request): View
    {
        $query = Department::withCount(['employees', 'activeEmployees'])
            ->with('manager');

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $departments = $query->paginate(15)->withQueryString();

        return view('modules.hr.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        $managers = Employee::active()
            ->whereHas('role', function ($query) {
                $query->whereIn('level', ['lead', 'manager']);
            })
            ->orderBy('first_name')
            ->get();

        return view('modules.hr.departments.create', compact('managers'));
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'code' => 'required|string|max:20|unique:hr_departments,code',
            'manager_id' => 'nullable|exists:hr_employees,id',
            'budget' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $department = Department::create($validated);

        return redirect()->route('modules.hr.departments.show', $department)
            ->with('success', __('hr.department_created_successfully'));
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department): View
    {
        $department->load([
            'manager',
            'employees' => function ($query) {
                $query->with('role')->orderBy('first_name');
            },
            'roles' => function ($query) {
                $query->withCount('employees');
            },
        ]);

        // Get department statistics
        $stats = [
            'total_employees' => $department->employees->count(),
            'active_employees' => $department->employees->where('status', 'active')->count(),
            'total_salary_expense' => $department->getTotalSalaryExpense(),
            'average_salary' => $department->getAverageSalary(),
            'budget_utilization' => $department->getBudgetUtilization(),
        ];

        // Get employees by role
        $employeesByRole = $department->employees
            ->where('status', 'active')
            ->groupBy('role.name')
            ->map->count();

        return view('modules.hr.departments.show', compact(
            'department',
            'stats',
            'employeesByRole'
        ));
    }

    /**
     * Show the form for editing the department.
     */
    public function edit(Department $department): View
    {
        $managers = Employee::active()
            ->where('id', '!=', $department->manager_id)
            ->whereHas('role', function ($query) {
                $query->whereIn('level', ['lead', 'manager']);
            })
            ->orderBy('first_name')
            ->get();

        return view('modules.hr.departments.edit', compact('department', 'managers'));
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'code' => ['required', 'string', 'max:20', Rule::unique('hr_departments')->ignore($department->id)],
            'manager_id' => 'nullable|exists:hr_employees,id',
            'budget' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $department->update($validated);

        return redirect()->route('modules.hr.departments.show', $department)
            ->with('success', __('hr.department_updated_successfully'));
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department): RedirectResponse
    {
        // Check if department has employees
        if ($department->employees()->exists()) {
            return back()->with('error', __('hr.cannot_delete_department_with_employees'));
        }

        $department->delete();

        return redirect()->route('modules.hr.departments.index')
            ->with('success', __('hr.department_deleted_successfully'));
    }

    /**
     * Get department performance data.
     */
    public function performance(Department $department)
    {
        $employees = $department->activeEmployees()->with('role')->get();

        $performance = [
            'employee_count' => $employees->count(),
            'average_salary' => $employees->avg('basic_salary') ?? 0,
            'total_salary' => $employees->sum('basic_salary'),
            'budget_utilization' => $department->getBudgetUtilization(),
            'employees_by_role' => $employees->groupBy('role.name')->map->count(),
            'salary_distribution' => [
                'min' => $employees->min('basic_salary') ?? 0,
                'max' => $employees->max('basic_salary') ?? 0,
                'avg' => $employees->avg('basic_salary') ?? 0,
            ],
        ];

        return response()->json($performance);
    }

    /**
     * Get department budget analysis.
     */
    public function budgetAnalysis(Department $department)
    {
        $totalSalary = $department->getTotalSalaryExpense();
        $budget = $department->budget ?? 0;
        $utilization = $department->getBudgetUtilization();

        $analysis = [
            'budget' => $budget,
            'total_salary_expense' => $totalSalary,
            'utilization_percentage' => $utilization,
            'remaining_budget' => max(0, $budget - $totalSalary),
            'over_budget' => $totalSalary > $budget,
            'can_add_employee' => $department->canAddEmployee(),
        ];

        return response()->json($analysis);
    }

    /**
     * Toggle department status.
     */
    public function toggleStatus(Department $department): RedirectResponse
    {
        $department->update(['is_active' => ! $department->is_active]);

        $status = $department->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Department has been {$status} successfully.");
    }

    /**
     * Get departments for select dropdown (AJAX).
     */
    public function getForSelect(Request $request)
    {
        $departments = Department::active()
            ->orderBy('name')
            ->get(['id', 'name', 'name_ar']);

        return response()->json($departments);
    }

    /**
     * Export departments to CSV.
     */
    public function export(Request $request)
    {
        $departments = Department::withCount(['employees', 'activeEmployees'])
            ->with('manager')
            ->get();

        $filename = 'departments_'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($departments) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Department Code',
                'Department Name',
                'Manager',
                'Total Employees',
                'Active Employees',
                'Budget',
                'Budget Utilization %',
                'Location',
                'Status',
            ]);

            // CSV data
            foreach ($departments as $department) {
                fputcsv($file, [
                    $department->code,
                    $department->display_name,
                    $department->manager ? $department->manager->display_name : 'N/A',
                    $department->employees_count,
                    $department->active_employees_count,
                    $department->budget ?? 0,
                    round($department->getBudgetUtilization(), 2),
                    $department->location ?? 'N/A',
                    $department->is_active ? 'Active' : 'Inactive',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
