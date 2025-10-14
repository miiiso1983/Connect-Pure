<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'accounting_employees';

    protected $fillable = [
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'hire_date',
        'termination_date',
        'status',
        'department',
        'position',
        'employment_type',
        'pay_type',
        'pay_rate',
        'currency',
        'pay_frequency',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'ssn',
        'tax_id',
        'birth_date',
        'marital_status',
        'dependents',
        'tax_withholdings',
        'benefits',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'birth_date' => 'date',
        'pay_rate' => 'decimal:2',
        'tax_withholdings' => 'array',
        'benefits' => 'array',
    ];

    protected $hidden = [
        'ssn',
    ];

    // Relationships
    public function payroll(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getFormattedPayRateAttribute()
    {
        $rate = number_format((float) $this->pay_rate, 2).' '.$this->currency;

        return $this->pay_type === 'hourly' ? $rate.'/hr' : $rate.'/year';
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'green',
            'inactive' => 'yellow',
            'terminated' => 'red',
            default => 'gray'
        };
    }

    public function getYearsOfServiceAttribute()
    {
        $endDate = $this->termination_date ?: now();

        return $this->hire_date->diffInYears($endDate);
    }

    // Methods
    public function calculateGrossPay($hours = null, $payPeriod = null)
    {
        if ($this->pay_type === 'salary') {
            $periodsPerYear = match ($this->pay_frequency) {
                'weekly' => 52,
                'bi_weekly' => 26,
                'semi_monthly' => 24,
                'monthly' => 12,
                default => 26
            };

            return $this->pay_rate / $periodsPerYear;
        } else {
            return ($hours ?: 40) * $this->pay_rate;
        }
    }

    public function getYearToDatePay($year = null)
    {
        $year = $year ?: now()->year;

        return $this->payroll()
            ->whereYear('pay_date', $year)
            ->sum('gross_pay');
    }

    public function getYearToDateTaxes($year = null)
    {
        $year = $year ?: now()->year;

        return $this->payroll()
            ->whereYear('pay_date', $year)
            ->sum('total_deductions');
    }

    public static function getStatuses()
    {
        return [
            'active' => __('accounting.active'),
            'inactive' => __('accounting.inactive'),
            'terminated' => __('accounting.terminated'),
        ];
    }

    public static function getEmploymentTypes()
    {
        return [
            'full_time' => __('accounting.full_time'),
            'part_time' => __('accounting.part_time'),
            'contract' => __('accounting.contract'),
            'intern' => __('accounting.intern'),
        ];
    }

    public static function getPayTypes()
    {
        return [
            'salary' => __('accounting.salary'),
            'hourly' => __('accounting.hourly'),
        ];
    }

    public static function getPayFrequencies()
    {
        return [
            'weekly' => __('accounting.weekly'),
            'bi_weekly' => __('accounting.bi_weekly'),
            'semi_monthly' => __('accounting.semi_monthly'),
            'monthly' => __('accounting.monthly'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (! $employee->employee_number) {
                $employee->employee_number = 'EMP-'.str_pad(
                    static::max('id') + 1, 6, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
