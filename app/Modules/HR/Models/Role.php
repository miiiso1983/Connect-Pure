<?php

namespace App\Modules\HR\Models;

use Database\Factories\HR\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return RoleFactory::new();
    }

    protected $table = 'hr_roles';

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'code',
        'department_id',
        'min_salary',
        'max_salary',
        'level',
        'responsibilities',
        'requirements',
        'is_active',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'responsibilities' => 'array',
        'requirements' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function activeEmployees(): HasMany
    {
        return $this->hasMany(Employee::class)->where('status', 'active');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_ar', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->name_ar
            ? $this->name_ar
            : $this->name;
    }

    public function getDisplayDescriptionAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->description_ar
            ? $this->description_ar
            : $this->description;
    }

    public function getFormattedSalaryRangeAttribute(): string
    {
        if ($this->min_salary && $this->max_salary) {
            return number_format((float) $this->min_salary, 0).' - '.number_format((float) $this->max_salary, 0);
        } elseif ($this->min_salary) {
            return 'من '.number_format((float) $this->min_salary, 0);
        } elseif ($this->max_salary) {
            return 'حتى '.number_format((float) $this->max_salary, 0);
        }

        return 'غير محدد';
    }

    public function getLevelTextAttribute(): string
    {
        return match ($this->level) {
            'junior' => __('hr.junior'),
            'mid' => __('hr.mid_level'),
            'senior' => __('hr.senior'),
            'lead' => __('hr.team_lead'),
            'manager' => __('hr.manager'),
            default => $this->level ?? __('hr.not_specified')
        };
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->count();
    }

    public function getActiveEmployeeCountAttribute(): int
    {
        return $this->activeEmployees()->count();
    }

    public function getIsVacantAttribute(): bool
    {
        return $this->activeEmployees()->count() === 0;
    }

    // Methods
    public function isSalaryInRange(float $salary): bool
    {
        $inRange = true;

        if ($this->min_salary && $salary < $this->min_salary) {
            $inRange = false;
        }

        if ($this->max_salary && $salary > $this->max_salary) {
            $inRange = false;
        }

        return $inRange;
    }

    public function getAverageSalary(): float
    {
        return $this->activeEmployees()->avg('basic_salary') ?? 0;
    }

    public function getSalaryBudget(): float
    {
        return $this->activeEmployees()->sum('basic_salary');
    }

    public function getResponsibilitiesList(): array
    {
        return $this->responsibilities ?? [];
    }

    public function getRequirementsList(): array
    {
        return $this->requirements ?? [];
    }

    public function canAcceptSalary(float $salary): array
    {
        $result = ['valid' => true, 'message' => ''];

        if ($this->min_salary && $salary < $this->min_salary) {
            $result['valid'] = false;
            $result['message'] = __('hr.salary_below_minimum', [
                'min' => number_format((float) $this->min_salary, 0),
            ]);
        }

        if ($this->max_salary && $salary > $this->max_salary) {
            $result['valid'] = false;
            $result['message'] = __('hr.salary_above_maximum', [
                'max' => number_format((float) $this->max_salary, 0),
            ]);
        }

        return $result;
    }

    // Static methods
    public static function getLevelOptions(): array
    {
        return [
            'junior' => __('hr.junior'),
            'mid' => __('hr.mid_level'),
            'senior' => __('hr.senior'),
            'lead' => __('hr.team_lead'),
            'manager' => __('hr.manager'),
        ];
    }

    public static function getActiveByDepartment(int $departmentId): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->byDepartment($departmentId)
            ->orderBy('name')
            ->get();
    }

    public static function getVacantRoles(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()
            ->whereDoesntHave('activeEmployees')
            ->with('department')
            ->get();
    }

    public static function getRoleStats(): array
    {
        return [
            'total_roles' => static::count(),
            'active_roles' => static::active()->count(),
            'vacant_roles' => static::getVacantRoles()->count(),
            'roles_by_level' => static::selectRaw('level, COUNT(*) as count')
                ->groupBy('level')
                ->pluck('count', 'level')
                ->toArray(),
        ];
    }
}
