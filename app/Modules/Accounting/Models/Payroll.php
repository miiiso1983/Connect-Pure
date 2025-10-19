<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;

    protected $table = 'accounting_payroll';

    protected $fillable = [
        'payroll_number',
        'employee_id',
        'pay_period_start',
        'pay_period_end',
        'pay_date',
        'status',
        'regular_hours',
        'overtime_hours',
        'holiday_hours',
        'sick_hours',
        'vacation_hours',
        'regular_pay',
        'overtime_pay',
        'holiday_pay',
        'sick_pay',
        'vacation_pay',
        'bonus',
        'commission',
        'other_earnings',
        'gross_pay',
        'federal_tax',
        'state_tax',
        'local_tax',
        'social_security',
        'medicare',
        'unemployment_tax',
        'health_insurance',
        'dental_insurance',
        'vision_insurance',
        'retirement_401k',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'currency',
        'notes',
        'calculation_details',
        'processed_at',
    ];

    protected $casts = [
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'pay_date' => 'date',
        'regular_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'holiday_hours' => 'decimal:2',
        'sick_hours' => 'decimal:2',
        'vacation_hours' => 'decimal:2',
        'regular_pay' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'holiday_pay' => 'decimal:2',
        'sick_pay' => 'decimal:2',
        'vacation_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'commission' => 'decimal:2',
        'other_earnings' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'federal_tax' => 'decimal:2',
        'state_tax' => 'decimal:2',
        'local_tax' => 'decimal:2',
        'social_security' => 'decimal:2',
        'medicare' => 'decimal:2',
        'unemployment_tax' => 'decimal:2',
        'health_insurance' => 'decimal:2',
        'dental_insurance' => 'decimal:2',
        'vision_insurance' => 'decimal:2',
        'retirement_401k' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'calculation_details' => 'array',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByPayPeriod($query, $start, $end)
    {
        return $query->where('pay_period_start', '>=', $start)
            ->where('pay_period_end', '<=', $end);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('pay_date', now()->year);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'gray',
            'processed' => 'blue',
            'paid' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedGrossPayAttribute()
    {
        return number_format((float) $this->gross_pay, 2).' '.$this->currency;
    }

    public function getFormattedNetPayAttribute()
    {
        return number_format((float) $this->net_pay, 2).' '.$this->currency;
    }

    public function getTotalHoursAttribute()
    {
        return $this->regular_hours + $this->overtime_hours + $this->holiday_hours + $this->sick_hours + $this->vacation_hours;
    }

    public function getTotalEarningsAttribute()
    {
        return $this->regular_pay + $this->overtime_pay + $this->holiday_pay + $this->sick_pay + $this->vacation_pay + $this->bonus + $this->commission + $this->other_earnings;
    }

    // Methods
    public function calculatePayroll()
    {
        $employee = $this->employee;

        // Calculate earnings
        if ($employee->pay_type === 'hourly') {
            $this->regular_pay = $this->regular_hours * $employee->pay_rate;
            $this->overtime_pay = $this->overtime_hours * ($employee->pay_rate * 1.5);
            $this->holiday_pay = $this->holiday_hours * ($employee->pay_rate * 2);
            $this->sick_pay = $this->sick_hours * $employee->pay_rate;
            $this->vacation_pay = $this->vacation_hours * $employee->pay_rate;
        } else {
            // Salary calculation
            $periodsPerYear = match ($employee->pay_frequency) {
                'weekly' => 52,
                'bi_weekly' => 26,
                'semi_monthly' => 24,
                'monthly' => 12,
                default => 26
            };
            $this->regular_pay = $employee->pay_rate / $periodsPerYear;
        }

        $this->gross_pay = $this->total_earnings;

        // Calculate taxes and deductions
        $this->calculateTaxes();
        $this->calculateDeductions();

        $this->total_deductions = $this->federal_tax + $this->state_tax + $this->local_tax +
                                 $this->social_security + $this->medicare + $this->unemployment_tax +
                                 $this->health_insurance + $this->dental_insurance + $this->vision_insurance +
                                 $this->retirement_401k + $this->other_deductions;

        $this->net_pay = $this->gross_pay - $this->total_deductions;

        $this->save();
    }

    private function calculateTaxes()
    {
        // Simplified tax calculations - in real implementation, use proper tax tables
        $this->federal_tax = $this->gross_pay * 0.12; // 12% federal
        $this->state_tax = $this->gross_pay * 0.05; // 5% state
        $this->social_security = $this->gross_pay * 0.062; // 6.2%
        $this->medicare = $this->gross_pay * 0.0145; // 1.45%
        $this->unemployment_tax = $this->gross_pay * 0.006; // 0.6%
    }

    private function calculateDeductions()
    {
        $benefits = $this->employee->benefits ?? [];

        $this->health_insurance = $benefits['health_insurance'] ?? 0;
        $this->dental_insurance = $benefits['dental_insurance'] ?? 0;
        $this->vision_insurance = $benefits['vision_insurance'] ?? 0;
        $this->retirement_401k = $benefits['retirement_401k'] ?? 0;
    }

    public function process()
    {
        $this->calculatePayroll();
        $this->status = 'processed';
        $this->processed_at = now();
        $this->save();
    }

    public function markAsPaid($paymentData = [])
    {
        $payment = $this->payments()->create(array_merge([
            'payment_number' => 'PAY-'.str_pad((string) (Payment::max('id') + 1), 6, '0', STR_PAD_LEFT),
            'type' => 'employee_payment',
            'employee_id' => $this->employee_id,
            'amount' => $this->net_pay,
            'currency' => $this->currency,
            'payment_date' => $this->pay_date,
            'status' => 'completed',
        ], $paymentData));

        $this->status = 'paid';
        $this->save();

        return $payment;
    }

    public static function getStatuses()
    {
        return [
            'draft' => __('accounting.draft'),
            'processed' => __('accounting.processed'),
            'paid' => __('accounting.paid'),
            'cancelled' => __('accounting.cancelled'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payroll) {
            if (! $payroll->payroll_number) {
                $payroll->payroll_number = 'PAY-'.date('Y').'-'.str_pad(
                    (string) (static::whereYear('created_at', date('Y'))->count() + 1), 4, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
