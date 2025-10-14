<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get roles assigned to this user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['assigned_at', 'assigned_by'])
            ->withTimestamps();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Check if user has all of the given roles.
     */
    public function hasAllRoles(array $roles): bool
    {
        $userRoles = $this->roles()->pluck('slug')->toArray();

        return empty(array_diff($roles, $userRoles));
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()->get()->some(function ($role) use ($permission) {
            return $role->hasPermission($permission);
        });
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->roles()->get()->some(function ($role) use ($permissions) {
            return $role->hasAnyPermission($permissions);
        });
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        $userPermissions = $this->getAllPermissions();

        return empty(array_diff($permissions, $userPermissions));
    }

    /**
     * Get all permissions for this user.
     */
    public function getAllPermissions(): array
    {
        $permissions = [];

        foreach ($this->roles as $role) {
            if ($role->permissions) {
                $permissions = array_merge($permissions, $role->permissions);
            }
        }

        return array_unique($permissions);
    }

    /**
     * Assign role to user.
     */
    public function assignRole(string $role, ?int $assignedBy = null): void
    {
        $roleModel = Role::where('slug', $role)->first();

        if ($roleModel && ! $this->hasRole($role)) {
            $this->roles()->attach($roleModel->id, [
                'assigned_at' => now(),
                'assigned_by' => $assignedBy,
            ]);
        }
    }

    /**
     * Remove role from user.
     */
    public function removeRole(string $role): void
    {
        $roleModel = Role::where('slug', $role)->first();

        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
        }
    }

    /**
     * Sync roles for user.
     */
    public function syncRoles(array $roles, ?int $assignedBy = null): void
    {
        $roleIds = Role::whereIn('slug', $roles)->pluck('id')->toArray();

        $syncData = [];
        foreach ($roleIds as $roleId) {
            $syncData[$roleId] = [
                'assigned_at' => now(),
                'assigned_by' => $assignedBy,
            ];
        }

        $this->roles()->sync($syncData);
    }

    /**
     * Get user's primary role (first assigned role).
     */
    public function getPrimaryRole(): ?Role
    {
        return $this->roles()->orderBy('user_roles.assigned_at')->first();
    }

    /**
     * Get user's role names.
     */
    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name')->toArray();
    }

    /**
     * Get user's localized role names.
     */
    public function getLocalizedRoleNames(): array
    {
        return $this->roles()->get()->map(function ($role) {
            return $role->localized_name;
        })->toArray();
    }

    /**
     * Check if user is in management (top or middle).
     */
    public function isManagement(): bool
    {
        return $this->hasAnyRole(['top_management', 'middle_management']);
    }

    /**
     * Check if user is top management.
     */
    public function isTopManagement(): bool
    {
        return $this->hasRole('top_management');
    }

    /**
     * Check if user can access module.
     */
    public function canAccessModule(string $module): bool
    {
        return $this->hasPermission($module.'.view');
    }
}
