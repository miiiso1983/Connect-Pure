<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
        'sort_order',
        'parent_id',
        'level',
        'path',
        'inherit_permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'parent_id' => 'integer',
        'level' => 'integer',
        'inherit_permissions' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($role) {
            // Prevent circular hierarchy
            if ($role->parent_id && $role->wouldCreateCircularHierarchy($role->parent_id)) {
                throw new \InvalidArgumentException('Cannot create circular hierarchy');
            }
        });

        static::saved(function ($role) {
            // Update hierarchy when parent changes
            if ($role->wasChanged('parent_id')) {
                $role->updateHierarchy();
            }
        });
    }

    /**
     * Get users assigned to this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot(['assigned_at', 'assigned_by'])
            ->withTimestamps();
    }

    /**
     * Get the parent role.
     */
    public function parent()
    {
        return $this->belongsTo(Role::class, 'parent_id');
    }

    /**
     * Get child roles.
     */
    public function children()
    {
        return $this->hasMany(Role::class, 'parent_id');
    }

    /**
     * Get all descendants (children, grandchildren, etc.).
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (parent, grandparent, etc.).
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get the localized name of the role.
     */
    public function getLocalizedNameAttribute(): string
    {
        return __('roles.' . $this->slug);
    }

    /**
     * Get the localized description of the role.
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        return __('roles.' . $this->slug . '_description');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions);
    }

    /**
     * Check if role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return !empty(array_intersect($permissions, $this->permissions));
    }

    /**
     * Check if role has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return empty(array_diff($permissions, $this->permissions));
    }

    /**
     * Add permission to role.
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            $this->save();
        }
    }

    /**
     * Remove permission from role.
     */
    public function removePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        
        if (($key = array_search($permission, $permissions)) !== false) {
            unset($permissions[$key]);
            $this->permissions = array_values($permissions);
            $this->save();
        }
    }

    /**
     * Sync permissions for role.
     */
    public function syncPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
        $this->save();
    }

    /**
     * Get all available permissions.
     */
    public static function getAllPermissions(): array
    {
        return [
            // Dashboard
            'dashboard.view',
            
            // HR Module
            'hr.view',
            'hr.employees.view',
            'hr.employees.create',
            'hr.employees.edit',
            'hr.employees.delete',
            'hr.departments.view',
            'hr.departments.manage',
            'hr.roles.view',
            'hr.roles.manage',
            'hr.leave.view',
            'hr.leave.create',
            'hr.leave.approve',
            'hr.leave.manage',
            'hr.attendance.view',
            'hr.attendance.manage',
            'hr.payroll.view',
            'hr.payroll.process',
            'hr.payroll.approve',
            'hr.reports.view',
            
            // Accounting Module
            'accounting.view',
            'accounting.entries.view',
            'accounting.entries.create',
            'accounting.entries.edit',
            'accounting.entries.delete',
            'accounting.entries.approve',
            'accounting.accounts.view',
            'accounting.accounts.manage',
            'accounting.reports.view',
            'accounting.reports.generate',
            'accounting.settings.manage',
            
            // CRM Module
            'crm.view',
            'crm.contacts.view',
            'crm.contacts.create',
            'crm.contacts.edit',
            'crm.contacts.delete',
            'crm.contacts.import',
            'crm.communications.view',
            'crm.communications.create',
            'crm.followups.view',
            'crm.followups.manage',
            'crm.reports.view',
            
            // Performance Module
            'performance.view',
            'performance.tasks.view',
            'performance.tasks.create',
            'performance.tasks.edit',
            'performance.tasks.delete',
            'performance.tasks.assign',
            'performance.metrics.view',
            'performance.metrics.manage',
            'performance.reports.view',
            'performance.analytics.view',
            
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.manage',
            'permissions.manage',
            
            // System Settings
            'settings.view',
            'settings.manage',
            'system.backup',
            'system.maintenance',
        ];
    }

    /**
     * Get permissions grouped by module.
     */
    public static function getGroupedPermissions(): array
    {
        $permissions = self::getAllPermissions();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission);
            $module = $parts[0];
            
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            
            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    /**
     * Get all permissions including inherited ones.
     */
    public function getEffectivePermissions(): array
    {
        $permissions = $this->permissions ?? [];

        if ($this->inherit_permissions && $this->parent) {
            $parentPermissions = $this->parent->getEffectivePermissions();
            $permissions = array_unique(array_merge($permissions, $parentPermissions));
        }

        return $permissions;
    }

    /**
     * Check if role has permission (including inherited).
     */
    public function hasPermissionWithInheritance(string $permission): bool
    {
        return in_array($permission, $this->getEffectivePermissions());
    }

    /**
     * Check if this role is a descendant of another role.
     */
    public function isDescendantOf(Role $role): bool
    {
        $parent = $this->parent;

        while ($parent) {
            if ($parent->id === $role->id) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * Check if this role is an ancestor of another role.
     */
    public function isAncestorOf(Role $role): bool
    {
        return $role->isDescendantOf($this);
    }

    /**
     * Get the root role (top-level parent).
     */
    public function getRoot(): Role
    {
        $role = $this;

        while ($role->parent) {
            $role = $role->parent;
        }

        return $role;
    }

    /**
     * Update hierarchy path and level.
     */
    public function updateHierarchy(): void
    {
        if ($this->parent) {
            $this->level = $this->parent->level + 1;
            $this->path = $this->parent->path ? $this->parent->path . '/' . $this->id : (string) $this->id;
        } else {
            $this->level = 0;
            $this->path = (string) $this->id;
        }

        $this->save();

        // Update children hierarchy
        foreach ($this->children as $child) {
            $child->updateHierarchy();
        }
    }

    /**
     * Scope to get root roles (no parent).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get roles by level.
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Get hierarchy tree structure.
     */
    public static function getHierarchyTree(): array
    {
        $roles = self::with('children')->roots()->orderBy('sort_order')->get();

        return $roles->map(function ($role) {
            return self::buildTree($role);
        })->toArray();
    }

    /**
     * Build tree structure recursively.
     */
    private static function buildTree(Role $role): array
    {
        $children = $role->children()->orderBy('sort_order')->get();

        return [
            'id' => $role->id,
            'name' => $role->name,
            'slug' => $role->slug,
            'level' => $role->level,
            'permissions_count' => count($role->permissions ?? []),
            'users_count' => $role->users()->count(),
            'children' => $children->map(function ($child) {
                return self::buildTree($child);
            })->toArray(),
        ];
    }

    /**
     * Check if setting a parent would create a circular hierarchy.
     */
    public function wouldCreateCircularHierarchy(int $parentId): bool
    {
        if ($parentId === $this->id) {
            return true;
        }

        $parent = self::find($parentId);
        if (!$parent) {
            return false;
        }

        return $parent->isDescendantOf($this);
    }

    /**
     * Scope for active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered roles.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get default role permissions for each role type.
     */
    public static function getDefaultPermissions(): array
    {
        return [
            'top_management' => [
                'dashboard.view',
                'hr.view', 'hr.employees.view', 'hr.departments.view', 'hr.roles.view',
                'hr.leave.view', 'hr.leave.approve', 'hr.attendance.view', 'hr.payroll.view',
                'hr.payroll.approve', 'hr.reports.view',
                'accounting.view', 'accounting.entries.view', 'accounting.entries.approve',
                'accounting.accounts.view', 'accounting.reports.view', 'accounting.reports.generate',
                'crm.view', 'crm.contacts.view', 'crm.communications.view', 'crm.reports.view',
                'performance.view', 'performance.tasks.view', 'performance.metrics.view',
                'performance.reports.view', 'performance.analytics.view',
                'users.view', 'roles.view', 'settings.view',
            ],
            'middle_management' => [
                'dashboard.view',
                'hr.view', 'hr.employees.view', 'hr.leave.view', 'hr.leave.approve',
                'hr.attendance.view', 'hr.payroll.view',
                'accounting.view', 'accounting.entries.view', 'accounting.reports.view',
                'crm.view', 'crm.contacts.view', 'crm.contacts.edit', 'crm.communications.view',
                'crm.communications.create', 'crm.followups.view', 'crm.followups.manage',
                'performance.view', 'performance.tasks.view', 'performance.tasks.create',
                'performance.tasks.edit', 'performance.tasks.assign', 'performance.metrics.view',
                'performance.reports.view',
            ],
            'sales_team' => [
                'dashboard.view',
                'crm.view', 'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
                'crm.communications.view', 'crm.communications.create', 'crm.followups.view',
                'crm.followups.manage', 'crm.reports.view',
                'performance.view', 'performance.tasks.view',
            ],
            'technical_team' => [
                'dashboard.view',
                'performance.view', 'performance.tasks.view', 'performance.tasks.create',
                'performance.tasks.edit', 'performance.metrics.view',
                'hr.view', 'hr.employees.view', 'hr.leave.view', 'hr.leave.create',
                'hr.attendance.view',
            ],
            'accounting' => [
                'dashboard.view',
                'accounting.view', 'accounting.entries.view', 'accounting.entries.create',
                'accounting.entries.edit', 'accounting.accounts.view', 'accounting.accounts.manage',
                'accounting.reports.view', 'accounting.reports.generate',
                'hr.view', 'hr.employees.view', 'hr.payroll.view', 'hr.payroll.process',
            ],
            'hr' => [
                'dashboard.view',
                'hr.view', 'hr.employees.view', 'hr.employees.create', 'hr.employees.edit',
                'hr.departments.view', 'hr.departments.manage', 'hr.roles.view', 'hr.roles.manage',
                'hr.leave.view', 'hr.leave.create', 'hr.leave.approve', 'hr.leave.manage',
                'hr.attendance.view', 'hr.attendance.manage', 'hr.payroll.view',
                'hr.payroll.process', 'hr.reports.view',
                'performance.view', 'performance.tasks.view', 'performance.metrics.view',
            ],
        ];
    }
}
