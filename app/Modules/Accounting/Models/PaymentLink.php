<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLink extends Model
{
    use HasFactory;

    protected $table = 'accounting_payment_links';

    protected $fillable = [
        'invoice_id',
        'token',
        'amount',
        'currency',
        'status',
        'expires_at',
        'paid_at',
        'created_by',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && now()->greaterThan($this->expires_at);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending');
    }
}

