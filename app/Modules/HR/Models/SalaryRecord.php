<?php

namespace App\Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryRecord extends Model
{
    use HasFactory;

    protected $table = 'hr_salary_records';

    protected $fillable = [
        'payroll_number',
        'employee_id',
        'period_start',
        'period_end',
        'year',
        'month',
        'working_days',
        'actual_working_days',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'food_allowance',
        'communication_allowance',
        'other_allowances',
        'allowance_details',
        'overtime_amount',
        'overtime_hours',
        'bonus',
        'commission',
        'bonus_details',
        'social_insurance',
        'income_tax',
        'loan_deduction',
        'advance_deduction',
        'absence_deduction',
        'late_deduction',
        'other_deductions',
        'deduction_details',
        'gross_salary',
        'total_deductions',
        'net_salary',
        'leave_days_taken',
        'leave_deduction',
        'remaining_annual_leave',
        'status',
        'prepared_by',
        'approved_by',
        'approved_at',
        'payment_date',
        'payment_method',
        'payment_reference',
        'accounting_entry_id',
        'is_posted_to_accounting',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'year' => 'integer',
        'month' => 'integer',
        'working_days' => 'integer',
        'actual_working_days' => 'integer',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'communication_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'allowance_details' => 'array',
        'overtime_amount' => 'decimal:2',
        'overtime_hours' => 'integer',
        'bonus' => 'decimal:2',
        'commission' => 'decimal:2',
        'bonus_details' => 'array',
        'social_insurance' => 'decimal:2',
        'income_tax' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'absence_deduction' => 'decimal:2',
        'late_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'deduction_details' => 'array',
        'gross_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'leave_days_taken' => 'integer',
        'leave_deduction' => 'decimal:2',
        'remaining_annual_leave' => 'integer',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
        'is_posted_to_accounting' => 'boolean',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'prepared_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function accountingEntry(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Accounting\Models\JournalEntry::class, 'accounting_entry_id');
    }

    // Scopes
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPeriod($query, $year, $month = null)
    {
        $query->where('year', $year);

        if ($month) {
            $query->where('month', $month);
        }

        return $query;
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeNotPostedToAccounting($query)
    {
        return $query->where('is_posted_to_accounting', false);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'approved' => 'blue',
            'paid' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'draft' => __('hr.draft'),
            'approved' => __('hr.approved'),
            'paid' => __('hr.paid'),
            'cancelled' => __('hr.cancelled'),
            default => $this->status
        };
    }

    public function getPaymentMethodTextAttribute(): string
    {
        return match ($this->payment_method) {
            'bank_transfer' => __('hr.bank_transfer'),
            'cash' => __('hr.cash'),
            'check' => __('hr.check'),
            default => $this->payment_method ?? __('hr.not_specified')
        };
    }

    public function getPeriodTextAttribute(): string
    {
        return $this->period_start->format('M Y');
    }

    public function getFormattedGrossSalaryAttribute(): string
    {
        return number_format($this->gross_salary, 2);
    }

    public function getFormattedNetSalaryAttribute(): string
    {
        return number_format($this->net_salary, 2);
    }

    public function getFormattedTotalDeductionsAttribute(): string
    {
        return number_format($this->total_deductions, 2);
    }

    public function getTotalAllowancesAttribute(): float
    {
        return $this->housing_allowance + $this->transport_allowance +
               $this->food_allowance + $this->communication_allowance +
               $this->other_allowances;
    }

    public function getCanBeEditedAttribute(): bool
    {
        return $this->status === 'draft';
    }

    public function getCanBeApprovedAttribute(): bool
    {
        return $this->status === 'draft';
    }

    public function getCanBePaidAttribute(): bool
    {
        return $this->status === 'approved';
    }

    // Methods
    public function calculateGrossSalary(): void
    {
        $this->gross_salary = $this->basic_salary + $this->total_allowances +
                             $this->overtime_amount + $this->bonus + $this->commission;
    }

    public function calculateTotalDeductions(): void
    {
        $this->total_deductions = $this->social_insurance + $this->income_tax +
                                 $this->loan_deduction + $this->advance_deduction +
                                 $this->absence_deduction + $this->late_deduction +
                                 $this->other_deductions + $this->leave_deduction;
    }

    public function calculateNetSalary(): void
    {
        $this->calculateGrossSalary();
        $this->calculateTotalDeductions();
        $this->net_salary = $this->gross_salary - $this->total_deductions;
    }

    public function approve(int $approverId): void
    {
        $this->status = 'approved';
        $this->approved_by = $approverId;
        $this->approved_at = now();
        $this->save();
    }

    public function markAsPaid(?string $paymentMethod = null, ?string $paymentReference = null): void
    {
        $this->status = 'paid';
        $this->payment_date = now();
        $this->payment_method = $paymentMethod;
        $this->payment_reference = $paymentReference;
        $this->save();
    }

    public function cancel(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function postToAccounting(): void
    {
        $service = new \App\Modules\HR\Services\PayrollAccountingService;
        $service->postSalaryToAccounting($this);
    }

    public function generatePayslip(): array
    {
        return [
            'employee' => $this->employee,
            'period' => $this->period_text,
            'payroll_number' => $this->payroll_number,
            'basic_salary' => $this->basic_salary,
            'allowances' => [
                'housing' => $this->housing_allowance,
                'transport' => $this->transport_allowance,
                'food' => $this->food_allowance,
                'communication' => $this->communication_allowance,
                'other' => $this->other_allowances,
                'total' => $this->total_allowances,
            ],
            'earnings' => [
                'overtime' => $this->overtime_amount,
                'bonus' => $this->bonus,
                'commission' => $this->commission,
            ],
            'deductions' => [
                'social_insurance' => $this->social_insurance,
                'income_tax' => $this->income_tax,
                'loan' => $this->loan_deduction,
                'advance' => $this->advance_deduction,
                'absence' => $this->absence_deduction,
                'late' => $this->late_deduction,
                'leave' => $this->leave_deduction,
                'other' => $this->other_deductions,
                'total' => $this->total_deductions,
            ],
            'summary' => [
                'gross_salary' => $this->gross_salary,
                'total_deductions' => $this->total_deductions,
                'net_salary' => $this->net_salary,
            ],
            'working_days' => [
                'scheduled' => $this->working_days,
                'actual' => $this->actual_working_days,
                'leave_taken' => $this->leave_days_taken,
            ],
            'leave_balance' => $this->remaining_annual_leave,
        ];
    }

    // Accounting integration methods moved to PayrollAccountingService

    // Static methods
    public static function generatePayrollNumber(): string
    {
        $lastRecord = static::orderBy('payroll_number', 'desc')->first();

        if ($lastRecord && preg_match('/PAY(\d+)/', $lastRecord->payroll_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1001;
        }

        return 'PAY'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function getPaymentMethodOptions(): array
    {
        return [
            'bank_transfer' => __('hr.bank_transfer'),
            'cash' => __('hr.cash'),
            'check' => __('hr.check'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'draft' => __('hr.draft'),
            'approved' => __('hr.approved'),
            'paid' => __('hr.paid'),
            'cancelled' => __('hr.cancelled'),
        ];
    }

    public static function getPayrollStats(?int $year = null, ?int $month = null): array
    {
        $query = static::query();

        if ($year) {
            $query->where('year', $year);
        }

        if ($month) {
            $query->where('month', $month);
        }

        return [
            'total_records' => $query->count(),
            'total_gross_salary' => $query->sum('gross_salary'),
            'total_net_salary' => $query->sum('net_salary'),
            'total_deductions' => $query->sum('total_deductions'),
            'average_salary' => $query->avg('net_salary') ?? 0,
            'records_by_status' => $query->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        ];
    }
}
