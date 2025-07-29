<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'accounting_payments';

    protected $fillable = [
        'payment_number',
        'type',
        'customer_id',
        'vendor_id',
        'employee_id',
        'invoice_id',
        'expense_id',
        'payroll_id',
        'payment_date',
        'amount',
        'currency',
        'exchange_rate',
        'method',
        'reference_number',
        'check_number',
        'transaction_id',
        'status',
        'deposit_account_id',
        'notes',
        'gateway_response',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'gateway_response' => 'array',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function depositAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'deposit_account_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('payment_date', now()->year);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'completed' => 'green',
            'failed' => 'red',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedAmountAttribute()
    {
        return number_format((float) $this->amount, 2) . ' ' . $this->currency;
    }

    public function getPayeeNameAttribute()
    {
        return match($this->type) {
            'customer_payment' => $this->customer?->display_name,
            'vendor_payment' => $this->vendor?->display_name,
            'employee_payment' => $this->employee?->full_name,
            default => __('accounting.unknown')
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'customer_payment' => 'green',
            'vendor_payment' => 'red',
            'employee_payment' => 'blue',
            'other' => 'gray',
            default => 'gray'
        };
    }

    // Methods
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
    }

    public function markAsFailed($reason = null)
    {
        $this->status = 'failed';
        if ($reason) {
            $this->notes = $reason;
        }
        $this->save();
    }

    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public static function getTypes()
    {
        return [
            'customer_payment' => __('accounting.customer_payment'),
            'vendor_payment' => __('accounting.vendor_payment'),
            'employee_payment' => __('accounting.employee_payment'),
            'other' => __('accounting.other'),
        ];
    }

    public static function getMethods()
    {
        return [
            'cash' => __('accounting.cash'),
            'check' => __('accounting.check'),
            'credit_card' => __('accounting.credit_card'),
            'bank_transfer' => __('accounting.bank_transfer'),
            'paypal' => __('accounting.paypal'),
            'stripe' => __('accounting.stripe'),
            'other' => __('accounting.other'),
        ];
    }

    public static function getStatuses()
    {
        return [
            'pending' => __('accounting.pending'),
            'completed' => __('accounting.completed'),
            'failed' => __('accounting.failed'),
            'cancelled' => __('accounting.cancelled'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->payment_number) {
                $payment->payment_number = 'PAY-' . date('Y') . '-' . str_pad(
                    static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
