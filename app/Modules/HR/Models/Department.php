<?php

namespace App\Modules\HR\Models;

use Database\Factories\HR\DepartmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return DepartmentFactory::new();
    }

    protected $table = 'hr_departments';

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'code',
        'manager_id',
        'budget',
        'location',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
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

    public function getFormattedBudgetAttribute(): string
    {
        return $this->budget ? number_format($this->budget, 2) : '0.00';
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->count();
    }

    public function getActiveEmployeeCountAttribute(): int
    {
        return $this->activeEmployees()->count();
    }

    // Methods
    public function getTotalSalaryExpense(): float
    {
        return $this->activeEmployees()->sum('basic_salary');
    }

    public function getAverageSalary(): float
    {
        return $this->activeEmployees()->avg('basic_salary') ?? 0;
    }

    public function getBudgetUtilization(): float
    {
        if (! $this->budget || $this->budget <= 0) {
            return 0;
        }

        $totalSalary = $this->getTotalSalaryExpense();

        return ($totalSalary / $this->budget) * 100;
    }

    public function canAddEmployee(): bool
    {
        if (! $this->budget) {
            return true; // No budget limit
        }

        return $this->getBudgetUtilization() < 100;
    }

    public function getVacantRoles(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->roles()->whereDoesntHave('employees')->get();
    }

    // Static methods
    public static function getActiveOptions(): array
    {
        return static::active()
            ->orderBy('name')
            ->get()
            ->pluck('display_name', 'id')
            ->toArray();
    }

    public static function getDepartmentStats(): array
    {
        return [
            'total_departments' => static::count(),
            'active_departments' => static::active()->count(),
            'total_employees' => Employee::count(),
            'departments_with_budget' => static::whereNotNull('budget')->count(),
            'average_department_size' => static::withCount('employees')->avg('employees_count') ?? 0,
        ];
    }

    public static function getDepartmentsByEmployeeCount(): \Illuminate\Database\Eloquent\Collection
    {
        return static::withCount(['employees' => function ($query) {
            $query->where('status', 'active');
        }])
            ->orderBy('employees_count', 'desc')
            ->get();
    }

    public static function getBudgetSummary(): array
    {
        $departments = static::whereNotNull('budget')->get();

        return [
            'total_budget' => $departments->sum('budget'),
            'total_salary_expense' => $departments->sum(function ($dept) {
                return $dept->getTotalSalaryExpense();
            }),
            'budget_utilization' => $departments->avg(function ($dept) {
                return $dept->getBudgetUtilization();
            }) ?? 0,
        ];
    }
}
