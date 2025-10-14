<?php

namespace App\Modules\Roles\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index()
    {
        // Get roles and permissions statistics
        $stats = [
            'total_roles' => Role::count(),
            'active_roles' => Role::where('is_active', true)->count(),
            'total_permissions' => $this->getTotalPermissions(),
            'active_users' => User::whereHas('roles')->count(),
            'users_without_roles' => User::whereDoesntHave('roles')->count(),
            'max_depth' => Role::max('level') ?? 0,
        ];

        // Get recent role assignments
        $recentAssignments = DB::table('user_roles')
            ->join('users', 'user_roles.user_id', '=', 'users.id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->select('users.name as user_name', 'users.email', 'roles.name as role_name', 'user_roles.assigned_at')
            ->orderBy('user_roles.assigned_at', 'desc')
            ->take(5)
            ->get();

        // Get roles with user counts
        $roles = Role::withCount('users')->orderBy('sort_order')->get();

        // Get permission groups
        $permissionGroups = $this->getPermissionGroups();

        return view('modules.roles.index', compact(
            'stats',
            'recentAssignments',
            'roles',
            'permissionGroups'
        ));
    }

    public function dashboard()
    {
        return view('modules.roles.dashboard');
    }

    public function roles()
    {
        $roles = Role::withCount('users')->orderBy('sort_order')->paginate(15);

        return view('modules.roles.roles.index', compact('roles'));
    }

    public function createRole()
    {
        $permissionGroups = $this->getPermissionGroups();
        $parentRoles = Role::where('is_active', true)->orderBy('level')->orderBy('name')->get();

        return view('modules.roles.roles.create', compact('permissionGroups', 'parentRoles'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'parent_id' => 'nullable|exists:roles,id',
            'inherit_permissions' => 'boolean',
        ]);

        Role::create($validated);

        return redirect()->route('modules.roles.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function showRole(Role $role)
    {
        $role->load('users');

        return view('modules.roles.roles.show', compact('role'));
    }

    public function editRole(Role $role)
    {
        $permissionGroups = $this->getPermissionGroups();
        $parentRoles = Role::where('is_active', true)
            ->where('id', '!=', $role->id)
            ->orderBy('level')
            ->orderBy('name')
            ->get()
            ->filter(function ($parentRole) use ($role) {
                return ! $parentRole->isDescendantOf($role);
            });

        return view('modules.roles.roles.edit', compact('role', 'permissionGroups', 'parentRoles'));
    }

    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,'.$role->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'parent_id' => 'nullable|exists:roles,id',
            'inherit_permissions' => 'boolean',
        ]);

        // Check for circular hierarchy
        if ($validated['parent_id'] && $role->wouldCreateCircularHierarchy($validated['parent_id'])) {
            return redirect()->back()
                ->withErrors(['parent_id' => 'Cannot create circular hierarchy'])
                ->withInput();
        }

        $role->update($validated);

        return redirect()->route('modules.roles.roles.show', $role)
            ->with('success', 'Role updated successfully.');
    }

    public function destroyRole(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete role that has assigned users.');
        }

        $role->delete();

        return redirect()->route('modules.roles.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function users()
    {
        $users = User::with('roles')->paginate(15);

        return view('modules.roles.users.index', compact('users'));
    }

    public function editUserRoles(User $user)
    {
        $roles = Role::where('is_active', true)->orderBy('sort_order')->get();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('modules.roles.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function updateUserRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($validated['roles'] ?? []);

        return redirect()->route('modules.roles.users.index')
            ->with('success', 'User roles updated successfully.');
    }

    public function permissions()
    {
        $permissionGroups = $this->getPermissionGroups();

        return view('modules.roles.permissions.index', compact('permissionGroups'));
    }

    public function hierarchy()
    {
        $hierarchyTree = Role::getHierarchyTree();
        $stats = [
            'total_roles' => Role::count(),
            'root_roles' => Role::roots()->count(),
            'max_depth' => Role::max('level') ?? 0,
            'roles_with_inheritance' => Role::where('inherit_permissions', true)->count(),
        ];

        return view('modules.roles.hierarchy.index', compact('hierarchyTree', 'stats'));
    }

    private function getTotalPermissions(): int
    {
        $allPermissions = Role::whereNotNull('permissions')->pluck('permissions')->toArray();
        $uniquePermissions = [];

        foreach ($allPermissions as $permissions) {
            if (is_array($permissions)) {
                $uniquePermissions = array_merge($uniquePermissions, $permissions);
            }
        }

        return count(array_unique($uniquePermissions));
    }

    private function getPermissionGroups(): array
    {
        return [
            'hr' => [
                'label' => 'Human Resources',
                'permissions' => [
                    'hr.view' => 'View HR Module',
                    'hr.employees.view' => 'View Employees',
                    'hr.employees.create' => 'Create Employees',
                    'hr.employees.edit' => 'Edit Employees',
                    'hr.employees.delete' => 'Delete Employees',
                    'hr.leave.view' => 'View Leave Requests',
                    'hr.leave.create' => 'Create Leave Requests',
                    'hr.leave.approve' => 'Approve Leave Requests',
                    'hr.leave.manage' => 'Manage Leave Requests',
                    'hr.departments.view' => 'View Departments',
                    'hr.departments.manage' => 'Manage Departments',
                    'hr.attendance.view' => 'View Attendance',
                    'hr.attendance.manage' => 'Manage Attendance',
                ],
            ],
            'crm' => [
                'label' => 'Customer Relationship Management',
                'permissions' => [
                    'crm.view' => 'View CRM Module',
                    'crm.leads.view' => 'View Leads',
                    'crm.leads.create' => 'Create Leads',
                    'crm.leads.edit' => 'Edit Leads',
                    'crm.leads.delete' => 'Delete Leads',
                    'crm.customers.view' => 'View Customers',
                    'crm.customers.create' => 'Create Customers',
                    'crm.customers.edit' => 'Edit Customers',
                    'crm.customers.delete' => 'Delete Customers',
                ],
            ],
            'performance' => [
                'label' => 'Performance Management',
                'permissions' => [
                    'performance.view' => 'View Performance Module',
                    'performance.tasks.view' => 'View Tasks',
                    'performance.tasks.create' => 'Create Tasks',
                    'performance.tasks.edit' => 'Edit Tasks',
                    'performance.tasks.delete' => 'Delete Tasks',
                    'performance.reports.view' => 'View Performance Reports',
                    'performance.analytics.view' => 'View Performance Analytics',
                ],
            ],
            'support' => [
                'label' => 'Support Management',
                'permissions' => [
                    'support.view' => 'View Support Module',
                    'support.tickets.view' => 'View Support Tickets',
                    'support.tickets.create' => 'Create Support Tickets',
                    'support.tickets.edit' => 'Edit Support Tickets',
                    'support.tickets.delete' => 'Delete Support Tickets',
                ],
            ],
            'admin' => [
                'label' => 'Administration',
                'permissions' => [
                    'admin.view' => 'View Admin Panel',
                    'admin.users.view' => 'View Users',
                    'admin.users.create' => 'Create Users',
                    'admin.users.edit' => 'Edit Users',
                    'admin.users.delete' => 'Delete Users',
                    'admin.roles.view' => 'View Roles',
                    'admin.roles.create' => 'Create Roles',
                    'admin.roles.edit' => 'Edit Roles',
                    'admin.roles.delete' => 'Delete Roles',
                    'admin.settings.view' => 'View Settings',
                    'admin.settings.edit' => 'Edit Settings',
                ],
            ],
        ];
    }
}
