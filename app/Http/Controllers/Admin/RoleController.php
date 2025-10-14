<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.manage')->except(['index', 'show']);
    }

    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::active()->ordered()->withCount('users')->get();
        $permissions = Role::getGroupedPermissions();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Role::getGroupedPermissions();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:'.implode(',', Role::getAllPermissions()),
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Role::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug.'-'.$counter;
            $counter++;
        }

        $role = Role::create($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', __('roles.role_created_successfully'));
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('users');
        $permissions = Role::getGroupedPermissions();

        return view('admin.roles.show', compact('role', 'permissions'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Role::getGroupedPermissions();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:'.implode(',', Role::getAllPermissions()),
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $role->name) {
            $validated['slug'] = Str::slug($validated['name']);

            // Ensure slug is unique
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Role::where('slug', $validated['slug'])->where('id', '!=', $role->id)->exists()) {
                $validated['slug'] = $originalSlug.'-'.$counter;
                $counter++;
            }
        }

        $role->update($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', __('roles.role_updated_successfully'));
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', __('roles.cannot_delete_role_with_users'));
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', __('roles.role_deleted_successfully'));
    }

    /**
     * Assign role to user.
     */
    public function assignUser(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if (! $user->hasRole($role->slug)) {
            $user->assignRole($role->slug, auth()->id());

            return response()->json([
                'success' => true,
                'message' => __('roles.role_assigned_successfully'),
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
    public function removeUser(Request $request, Role $role)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validated['user_id']);

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
     * Get users for role assignment.
     */
    public function getUsers(Role $role)
    {
        $assignedUserIds = $role->users()->pluck('users.id');

        $availableUsers = User::whereNotIn('id', $assignedUserIds)
            ->select('id', 'name', 'email')
            ->get();

        $assignedUsers = $role->users()
            ->select('users.id', 'users.name', 'users.email', 'user_roles.assigned_at')
            ->get();

        return response()->json([
            'available_users' => $availableUsers,
            'assigned_users' => $assignedUsers,
        ]);
    }

    /**
     * Update role permissions.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:'.implode(',', Role::getAllPermissions()),
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => __('roles.permissions_updated_successfully'),
        ]);
    }

    /**
     * Get permission matrix for all roles.
     */
    public function permissionMatrix()
    {
        $roles = Role::active()->ordered()->get();
        $permissions = Role::getGroupedPermissions();

        return view('admin.roles.permission-matrix', compact('roles', 'permissions'));
    }

    /**
     * Bulk assign permissions to multiple roles.
     */
    public function bulkUpdatePermissions(Request $request)
    {
        $validated = $request->validate([
            'role_permissions' => 'required|array',
            'role_permissions.*' => 'array',
            'role_permissions.*.*' => 'string|in:'.implode(',', Role::getAllPermissions()),
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['role_permissions'] as $roleId => $permissions) {
                $role = Role::findOrFail($roleId);
                $role->syncPermissions($permissions);
            }
        });

        return response()->json([
            'success' => true,
            'message' => __('roles.bulk_permissions_updated_successfully'),
        ]);
    }

    /**
     * Clone role with its permissions.
     */
    public function clone(Role $role)
    {
        $newRole = $role->replicate();
        $newRole->name = $role->name.' (Copy)';
        $newRole->slug = $role->slug.'-copy';

        // Ensure slug is unique
        $originalSlug = $newRole->slug;
        $counter = 1;
        while (Role::where('slug', $newRole->slug)->exists()) {
            $newRole->slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        $newRole->save();

        return redirect()->route('admin.roles.edit', $newRole)
            ->with('success', __('roles.role_cloned_successfully'));
    }
}
