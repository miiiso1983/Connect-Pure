<?php

namespace App\Modules\HR\Models;

use App\Models\User;
use Database\Factories\HR\EmployeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return EmployeeFactory::new();
    }

    protected $table = 'hr_employees';

    protected $fillable = [
        'employee_number',
        'user_id',
        'first_name',
        'last_name',
        'first_name_ar',
        'last_name_ar',
        'email',
        'phone',
        'mobile',
        'date_of_birth',
        'gender',
        'marital_status',
        'nationality',
        'national_id',
        'passport_number',
        'address',
        'address_ar',
        'city',
        'state',
        'postal_code',
        'country',
        'department_id',
        'role_id',
        'manager_id',
        'hire_date',
        'probation_end_date',
        'employment_type',
        'status',
        'termination_date',
        'termination_reason',
        'basic_salary',
        'allowances',
        'bank_name',
        'bank_account_number',
        'iban',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'profile_photo',
        'documents',
        'annual_leave_balance',
        'sick_leave_balance',
        'emergency_leave_balance',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'probation_end_date' => 'date',
        'termination_date' => 'date',
        'basic_salary' => 'decimal:2',
        'allowances' => 'array',
        'documents' => 'array',
        'annual_leave_balance' => 'integer',
        'sick_leave_balance' => 'integer',
        'emergency_leave_balance' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceRecords(): HasMany
    {
        // Alias to maintain compatibility with views/tests expecting attendanceRecords()
        return $this->hasMany(Attendance::class);
    }


    public function salaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class);
    }

    public function approvedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'approver_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('first_name_ar', 'like', "%{$search}%")
                ->orWhere('last_name_ar', 'like', "%{$search}%")
                ->orWhere('employee_number', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getFullNameArAttribute(): string
    {
        return trim(($this->first_name_ar ?? $this->first_name).' '.($this->last_name_ar ?? $this->last_name));
    }

    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->full_name_ar : $this->full_name;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getYearsOfServiceAttribute(): float
    {
        return $this->hire_date->diffInYears(now(), true);
    }

    public function getMonthsOfServiceAttribute(): int
    {
        return $this->hire_date->diffInMonths(now());
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'inactive' => 'yellow',
            'terminated' => 'red',
            'resigned' => 'gray',
            default => 'gray'
        };
    }

    public function getEmploymentTypeTextAttribute(): string
    {
        return match ($this->employment_type) {
            'full_time' => __('hr.full_time'),
            'part_time' => __('hr.part_time'),
            'contract' => __('hr.contract'),
            'intern' => __('hr.intern'),
            default => $this->employment_type
        };
    }

    public function getGenderTextAttribute(): string
    {
        return match ($this->gender) {
            'male' => __('hr.male'),
            'female' => __('hr.female'),
            default => __('hr.not_specified')
        };
    }

    public function getMaritalStatusTextAttribute(): string
    {
        return match ($this->marital_status) {
            'single' => __('hr.single'),
            'married' => __('hr.married'),
            'divorced' => __('hr.divorced'),
            'widowed' => __('hr.widowed'),
            default => __('hr.not_specified')
        };
    }

    public function getFormattedSalaryAttribute(): string
    {
        return number_format((float) $this->basic_salary, 2);
    }

    public function getIsOnProbationAttribute(): bool
    {
        return $this->probation_end_date && $this->probation_end_date > now();
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    // Methods
    public function getTotalAllowances(): float
    {
        if (! $this->allowances) {
            return 0;
        }

        return collect($this->allowances)->sum();
    }

    public function getGrossSalary(): float
    {
        return $this->basic_salary + $this->getTotalAllowances();
    }

    public function canApproveLeave(): bool
    {
        return $this->subordinates()->exists() || $this->role->level === 'manager';
    }

    public function getLeaveBalance(string $leaveType): int
    {
        return match ($leaveType) {
            'annual' => $this->annual_leave_balance,
            'sick' => $this->sick_leave_balance,
            'emergency' => $this->emergency_leave_balance,
            default => 0
        };
    }

    public function deductLeaveBalance(string $leaveType, int $days): void
    {
        match ($leaveType) {
            'annual' => $this->decrement('annual_leave_balance', $days),
            'sick' => $this->decrement('sick_leave_balance', $days),
            'emergency' => $this->decrement('emergency_leave_balance', $days),
        };
    }

    public function restoreLeaveBalance(string $leaveType, int $days): void
    {
        match ($leaveType) {
            'annual' => $this->increment('annual_leave_balance', $days),
            'sick' => $this->increment('sick_leave_balance', $days),
            'emergency' => $this->increment('emergency_leave_balance', $days),
        };
    }

    public function getAttendanceForMonth(int $year, int $month): \Illuminate\Database\Eloquent\Collection
    {
        return $this->attendance()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
    }

    public function getLatestSalaryRecord(): ?SalaryRecord
    {
        return $this->salaryRecords()->latest('period_end')->first();
    }

    public function hasActiveSalaryRecord(int $year, int $month): bool
    {
        return $this->salaryRecords()
            ->where('year', $year)
            ->where('month', $month)
            ->exists();
    }

    // Static methods
    public static function generateEmployeeNumber(): string
    {
        $lastEmployee = static::orderBy('employee_number', 'desc')->first();

        if ($lastEmployee && preg_match('/EMP(\d+)/', $lastEmployee->employee_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1001;
        }

        return 'EMP'.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function getEmploymentTypeOptions(): array
    {
        return [
            'full_time' => __('hr.full_time'),
            'part_time' => __('hr.part_time'),
            'contract' => __('hr.contract'),
            'intern' => __('hr.intern'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'active' => __('hr.active'),
            'inactive' => __('hr.inactive'),
            'terminated' => __('hr.terminated'),
            'resigned' => __('hr.resigned'),
        ];
    }

    public static function getGenderOptions(): array
    {
        return [
            'male' => __('hr.male'),
            'female' => __('hr.female'),
        ];
    }

    public static function getMaritalStatusOptions(): array
    {
        return [
            'single' => __('hr.single'),
            'married' => __('hr.married'),
            'divorced' => __('hr.divorced'),
            'widowed' => __('hr.widowed'),
        ];
    }

    public static function getEmployeeStats(): array
    {
        return [
            'total_employees' => static::count(),
            'active_employees' => static::active()->count(),
            'on_probation' => static::active()->where('probation_end_date', '>', now())->count(),
            'average_salary' => static::active()->avg('basic_salary') ?? 0,
            'total_salary_expense' => static::active()->sum('basic_salary'),
            'employees_by_department' => static::active()
                ->with('department')
                ->get()
                ->groupBy('department.name')
                ->map->count()
                ->toArray(),
        ];
    }
}
