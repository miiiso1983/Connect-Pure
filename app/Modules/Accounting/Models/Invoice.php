<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, InvoiceItem> $items
 */
class Invoice extends Model
{
    use HasFactory;

    protected $table = 'accounting_invoices';

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'invoice_date',
        'due_date',
        'status',
        'currency',
        'exchange_rate',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'payment_terms',
        'notes',
        'terms_conditions',
        'billing_address',
        'shipping_address',
        'reference_number',
        'po_number',
        'is_recurring',
        'recurring_profile_id',
        'sent_at',
        'viewed_at',
        'paid_at',
        'whatsapp_sent_at',
        'whatsapp_message_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'is_recurring' => 'boolean',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'paid_at' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentLinks(): HasMany
    {
        return $this->hasMany(\App\Modules\Accounting\Models\PaymentLink::class);
    }


    public function recurringProfile(): BelongsTo
    {
        return $this->belongsTo(RecurringProfile::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
            ->where('due_date', '<', now());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('invoice_date', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereBetween('invoice_date', [now()->startOfYear(), now()->endOfYear()]);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'viewed' => 'yellow',
            'partial' => 'orange',
            'paid' => 'green',
            'overdue' => 'red',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedTotalAttribute()
    {
        return number_format((float) $this->total_amount, 2).' '.$this->currency;
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format((float) $this->balance_due, 2).' '.$this->currency;
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->status === 'paid' || $this->due_date >= now()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }

    // Methods
    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('amount');
        $this->tax_amount = $this->items->sum('tax_amount');
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->balance_due = $this->total_amount - $this->paid_amount;
        $this->save();
    }

    public function markAsSent()
    {
        $this->status = 'sent';
        $this->sent_at = now();
        $this->save();
    }

    public function markAsViewed()
    {
        if ($this->status === 'sent') {
            $this->status = 'viewed';
            $this->viewed_at = now();
            $this->save();
        }
    }

    public function addPayment($amount, $paymentData = [])
    {
        $payment = $this->payments()->create(array_merge([
            'payment_number' => 'PAY-'.str_pad((string) (Payment::max('id') + 1), 6, '0', STR_PAD_LEFT),
            'type' => 'customer_payment',
            'customer_id' => $this->customer_id,
            'amount' => $amount,
            'currency' => $this->currency,
            'payment_date' => now(),
            'status' => 'completed',
        ], $paymentData));

        $this->paid_amount += $amount;
        $this->balance_due = $this->total_amount - $this->paid_amount;

        if ($this->balance_due <= 0) {
            $this->status = 'paid';
            $this->paid_at = now();
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        }

        $this->save();
        $this->customer->updateBalance(-$amount);

        return $payment;
    }

    public static function getStatuses()
    {
        return [
            'draft' => __('accounting.draft'),
            'sent' => __('accounting.sent'),
            'viewed' => __('accounting.viewed'),
            'partial' => __('accounting.partial'),
            'paid' => __('accounting.paid'),
            'overdue' => __('accounting.overdue'),
            'cancelled' => __('accounting.cancelled'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (! $invoice->invoice_number) {
                $currentYear = date('Y');
                $startOfYear = now()->startOfYear();
                $endOfYear = now()->endOfYear();

                $invoice->invoice_number = 'INV-'.$currentYear.'-'.str_pad(
                    (string) (static::whereBetween('created_at', [$startOfYear, $endOfYear])->count() + 1), 4, '0', STR_PAD_LEFT
                );
            }
        });

        static::saved(function ($invoice) {
            if ($invoice->wasChanged(['status']) && $invoice->status === 'overdue') {
                // Update status to overdue for invoices past due date
                static::where('status', '!=', 'paid')
                    ->where('due_date', '<', now())
                    ->update(['status' => 'overdue']);
            }
        });
    }
}
