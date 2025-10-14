<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read \App\Modules\Accounting\Models\Vendor|null $vendor
 * @property-read \App\Modules\Accounting\Models\Employee|null $employee
 */
class Expense extends Model
{
    use HasFactory;

    protected $table = 'accounting_expenses';

    protected $fillable = [
        'expense_number',
        'vendor_id',
        'account_id',
        'employee_id',
        'expense_date',
        'status',
        'category',
        'description',
        'amount',
        'currency',
        'exchange_rate',
        'tax_amount',
        'total_amount',
        'payment_method',
        'reference_number',
        'receipt_number',
        'is_billable',
        'customer_id',
        'is_reimbursable',
        'is_recurring',
        'notes',
        'attachments',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_billable' => 'boolean',
        'is_reimbursable' => 'boolean',
        'is_recurring' => 'boolean',
        'attachments' => 'array',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeBillable($query)
    {
        return $query->where('is_billable', true);
    }

    public function scopeReimbursable($query)
    {
        return $query->where('is_reimbursable', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('expense_date', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereBetween('expense_date', [now()->startOfYear(), now()->endOfYear()]);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'gray',
            'pending' => 'yellow',
            'approved' => 'blue',
            'paid' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedAmountAttribute()
    {
        return number_format((float) $this->amount, 2).' '.$this->currency;
    }

    public function getFormattedTotalAttribute()
    {
        return number_format((float) $this->total_amount, 2).' '.$this->currency;
    }

    public function getVendorNameAttribute()
    {
        return $this->vendor ? $this->vendor->name : __('accounting.no_vendor');
    }

    public function getEmployeeNameAttribute()
    {
        if (! $this->employee) {
            return null;
        }
        $first = $this->employee->first_name ?? '';
        $last = $this->employee->last_name ?? '';
        $full = trim($first.' '.$last);

        return $full !== '' ? $full : null;
    }

    // Methods
    public function approve($approvedBy = null)
    {
        $this->status = 'approved';
        $this->approved_at = now();
        $this->approved_by = $approvedBy;
        $this->save();
    }

    public function reject()
    {
        $this->status = 'rejected';
        $this->save();
    }

    public function markAsPaid($paymentData = [])
    {
        $payment = $this->payments()->create(array_merge([
            'payment_number' => 'PAY-'.str_pad(Payment::max('id') + 1, 6, '0', STR_PAD_LEFT),
            'type' => 'vendor_payment',
            'vendor_id' => $this->vendor_id,
            'amount' => $this->total_amount,
            'currency' => $this->currency,
            'payment_date' => now(),
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
            'pending' => __('accounting.pending'),
            'approved' => __('accounting.approved'),
            'paid' => __('accounting.paid'),
            'rejected' => __('accounting.rejected'),
        ];
    }

    public static function getCategories()
    {
        return [
            'office_supplies' => __('accounting.office_supplies'),
            'travel' => __('accounting.travel'),
            'meals' => __('accounting.meals'),
            'utilities' => __('accounting.utilities'),
            'rent' => __('accounting.rent'),
            'insurance' => __('accounting.insurance'),
            'marketing' => __('accounting.marketing'),
            'professional_services' => __('accounting.professional_services'),
            'equipment' => __('accounting.equipment'),
            'software' => __('accounting.software'),
            'other' => __('accounting.other'),
        ];
    }

    public static function getPaymentMethods()
    {
        return [
            'cash' => __('accounting.cash'),
            'check' => __('accounting.check'),
            'credit_card' => __('accounting.credit_card'),
            'bank_transfer' => __('accounting.bank_transfer'),
            'other' => __('accounting.other'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            if (! $expense->expense_number) {
                $currentYear = date('Y');
                $startOfYear = now()->startOfYear();
                $endOfYear = now()->endOfYear();

                $expense->expense_number = 'EXP-'.$currentYear.'-'.str_pad(
                    static::whereBetween('created_at', [$startOfYear, $endOfYear])->count() + 1, 4, '0', STR_PAD_LEFT
                );
            }

            if (! $expense->total_amount) {
                $expense->total_amount = $expense->amount + $expense->tax_amount;
            }
        });
    }
}
