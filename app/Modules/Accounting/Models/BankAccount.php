<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'bank_name',
        'account_number',
        'routing_number',
        'account_type',
        'currency',
        'opening_balance',
        'current_balance',
        'chart_account_id',
        'is_active',
        'description',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function chartAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_account_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    // Accessors
    public function getFormattedBalanceAttribute(): string
    {
        return $this->currency.' '.number_format((float) $this->current_balance, 2);
    }

    public function getMaskedAccountNumberAttribute(): string
    {
        if (strlen($this->account_number) <= 4) {
            return $this->account_number;
        }

        return '****'.substr($this->account_number, -4);
    }

    public function getAccountTypeTextAttribute(): string
    {
        return match ($this->account_type) {
            'checking' => __('accounting.checking_account'),
            'savings' => __('accounting.savings_account'),
            'credit_card' => __('accounting.credit_card'),
            'line_of_credit' => __('accounting.line_of_credit'),
            default => $this->account_type
        };
    }

    // Methods
    public function updateBalance(float $amount): void
    {
        $this->current_balance += $amount;
        $this->save();
    }

    public function reconcile(array $transactions): void
    {
        // Bank reconciliation logic would go here
        // This is a simplified version
        foreach ($transactions as $transaction) {
            BankTransaction::create([
                'bank_account_id' => $this->id,
                'transaction_date' => $transaction['date'],
                'description' => $transaction['description'],
                'amount' => $transaction['amount'],
                'type' => $transaction['type'],
                'reference' => $transaction['reference'] ?? null,
                'is_reconciled' => true,
            ]);
        }
    }

    // Static methods
    public static function getAccountTypeOptions(): array
    {
        return [
            'checking' => __('accounting.checking_account'),
            'savings' => __('accounting.savings_account'),
            'credit_card' => __('accounting.credit_card'),
            'line_of_credit' => __('accounting.line_of_credit'),
        ];
    }
}
