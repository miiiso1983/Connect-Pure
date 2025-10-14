<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'vendor_id',
        'bill_date',
        'due_date',
        'reference_number',
        'subtotal',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'currency',
        'exchange_rate',
        'status',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'received_at' => 'datetime',
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('payment_type', 'vendor_payment');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', ['sent', 'received', 'partial']);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'received' => 'yellow',
            'partial' => 'orange',
            'paid' => 'green',
            'overdue' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->currency.' '.number_format((float) $this->total_amount, 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now() && in_array($this->status, ['sent', 'received', 'partial']);
    }

    // Methods
    public function applyPayment(float $amount): void
    {
        $this->paid_amount += $amount;
        $this->balance_due = $this->total_amount - $this->paid_amount;

        if ($this->balance_due <= 0) {
            $this->status = 'paid';
            $this->balance_due = 0;
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        }

        $this->save();
    }

    public function reversePayment(float $amount): void
    {
        $this->paid_amount -= $amount;
        $this->balance_due = $this->total_amount - $this->paid_amount;

        if ($this->paid_amount <= 0) {
            $this->status = 'received';
            $this->paid_amount = 0;
        } else {
            $this->status = 'partial';
        }

        $this->save();
    }
}
