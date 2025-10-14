<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.view')->only(['index', 'show']);
        $this->middleware('permission:users.edit|roles.manage')->except(['index', 'show']);
    }

    /**
     * Display user role management interface.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(20);
        $roles = Role::active()->ordered()->get();

        return view('admin.user-roles.index', compact('users', 'roles'));
    }

    /**
     * Show user role assignment form.
     */
    public function show(User $user)
    {
        $user->load('roles');
        $availableRoles = Role::active()->ordered()->get();

        return view('admin.user-roles.show', compact('user', 'availableRoles'));
    }

    /**
     * Update user roles.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $roleIds = $validated['roles'] ?? [];
        $roles = Role::whereIn('id', $roleIds)->pluck('slug')->toArray();

        $user->syncRoles($roles, auth()->id());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('roles.user_roles_updated_successfully'),
                'user_roles' => $user->fresh()->getLocalizedRoleNames(),
            ]);
        }

        return redirect()->route('admin.user-roles.show', $user)
            ->with('success', __('roles.user_roles_updated_successfully'));
    }

    /**
     * Assign single role to user.
     */
    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        if (! $user->hasRole($role->slug)) {
            $user->assignRole($role->slug, auth()->id());

            return response()->json([
                'success' => true,
                'message' => __('roles.role_assigned_successfully'),
                'role' => [
                    'id' => $role->id,
                    'name' => $role->localized_name,
                    'slug' => $role->slug,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('roles.user_already_has_role'),
        ]);
    }

    /**
     * Remove role from user.
     */
    public function removeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        if ($user->hasRole($role->slug)) {
            $user->removeRole($role->slug);

            return response()->json([
                'success' => true,
                'message' => __('roles.role_removed_successfully'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('roles.user_does_not_have_role'),
        ]);
    }

    /**
     * Bulk assign roles to multiple users.
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $roles = Role::whereIn('id', $validated['role_ids'])->pluck('slug')->toArray();

        DB::transaction(function () use ($users, $roles) {
            foreach ($users as $user) {
                foreach ($roles as $role) {
                    if (! $user->hasRole($role)) {
                        $user->assignRole($role, auth()->id());
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => __('roles.bulk_roles_assigned_successfully'),
            'affected_users' => count($users),
            'assigned_roles' => count($roles),
        ]);
    }

    /**
     * Bulk remove roles from multiple users.
     */
    public function bulkRemove(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $roles = Role::whereIn('id', $validated['role_ids'])->pluck('slug')->toArray();

        DB::transaction(function () use ($users, $roles) {
            foreach ($users as $user) {
                foreach ($roles as $role) {
                    if ($user->hasRole($role)) {
                        $user->removeRole($role);
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => __('roles.bulk_roles_removed_successfully'),
            'affected_users' => count($users),
            'removed_roles' => count($roles),
        ]);
    }

    /**
     * Get user permissions summary.
     */
    public function permissions(User $user)
    {
        $user->load('roles');
        $permissions = $user->getAllPermissions();
        $groupedPermissions = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission);
            $module = $parts[0];

            if (! isset($groupedPermissions[$module])) {
                $groupedPermissions[$module] = [];
            }

            $groupedPermissions[$module][] = $permission;
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->localized_name,
                    'slug' => $role->slug,
                    'permissions_count' => count($role->permissions ?? []),
                ];
            }),
            'permissions' => $permissions,
            'grouped_permissions' => $groupedPermissions,
            'permissions_count' => count($permissions),
        ]);
    }

    /**
     * Search users for role assignment.
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');
        $roleId = $request->get('role_id');

        $usersQuery = User::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
        });

        if ($roleId) {
            $role = Role::findOrFail($roleId);
            $assignedUserIds = $role->users()->pluck('users.id');
            $usersQuery->whereNotIn('id', $assignedUserIds);
        }

        $users = $usersQuery->select('id', 'name', 'email')
            ->limit(20)
            ->get();

        return response()->json([
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'display_name' => $user->name.' ('.$user->email.')',
                ];
            }),
        ]);
    }

    /**
     * Get role hierarchy and user distribution.
     */
    public function hierarchy()
    {
        $roles = Role::active()->ordered()->withCount('users')->get();

        $hierarchy = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->localized_name,
                'slug' => $role->slug,
                'users_count' => $role->users_count,
                'permissions_count' => count($role->permissions ?? []),
                'sort_order' => $role->sort_order,
            ];
        });

        return response()->json([
            'hierarchy' => $hierarchy,
            'total_users' => User::count(),
            'total_roles' => $roles->count(),
        ]);
    }

    /**
     * Export user roles data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $users = User::with('roles')->get();

        $data = $users->map(function ($user) {
            return [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Roles' => $user->getLocalizedRoleNames(),
                'Permissions Count' => count($user->getAllPermissions()),
                'Created At' => $user->created_at->format('Y-m-d H:i:s'),
            ];
        });

        if ($format === 'json') {
            return response()->json($data);
        }

        // CSV Export
        $filename = 'user_roles_'.date('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
                foreach ($data as $row) {
                    fputcsv($file, array_values($row));
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
