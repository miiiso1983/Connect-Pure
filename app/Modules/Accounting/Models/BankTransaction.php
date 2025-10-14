<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransaction extends Model
{
    use HasFactory;

    protected $table = 'accounting_bank_transactions';

    protected $fillable = [
        'bank_account_id',
        'transaction_date',
        'transaction_type',
        'amount',
        'balance_after',
        'description',
        'reference_number',
        'category',
        'payee',
        'is_reconciled',
        'reconciled_at',
        'reconciled_by',
        'notes',
        'attachment_path',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'is_reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
    ];

    protected $dates = [
        'transaction_date',
        'reconciled_at',
        'created_at',
        'updated_at',
    ];

    // Relationships
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reconciled_by');
    }

    // Scopes
    public function scopeDebit($query)
    {
        return $query->where('transaction_type', 'debit');
    }

    public function scopeCredit($query)
    {
        return $query->where('transaction_type', 'credit');
    }

    public function scopeReconciled($query)
    {
        return $query->where('is_reconciled', true);
    }

    public function scopeUnreconciled($query)
    {
        return $query->where('is_reconciled', false);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors & Mutators
    public function getFormattedAmountAttribute()
    {
        $prefix = $this->transaction_type === 'debit' ? '-' : '+';

        return $prefix.number_format($this->amount, 2);
    }

    public function getTransactionTypeColorAttribute()
    {
        return $this->transaction_type === 'debit' ? 'red' : 'green';
    }

    public function getIsDebitAttribute()
    {
        return $this->transaction_type === 'debit';
    }

    public function getIsCreditAttribute()
    {
        return $this->transaction_type === 'credit';
    }

    // Methods
    public function reconcile(int $userId): void
    {
        $this->update([
            'is_reconciled' => true,
            'reconciled_at' => now(),
            'reconciled_by' => $userId,
        ]);
    }

    public function unreconcile(): void
    {
        $this->update([
            'is_reconciled' => false,
            'reconciled_at' => null,
            'reconciled_by' => null,
        ]);
    }

    public function updateBalance(): void
    {
        $previousBalance = $this->bankAccount->current_balance;

        if ($this->transaction_type === 'credit') {
            $newBalance = $previousBalance + $this->amount;
        } else {
            $newBalance = $previousBalance - $this->amount;
        }

        $this->update(['balance_after' => $newBalance]);
        $this->bankAccount->update(['current_balance' => $newBalance]);
    }

    // Static methods
    public static function getTransactionTypes(): array
    {
        return [
            'debit' => 'Debit',
            'credit' => 'Credit',
        ];
    }

    public static function getCategories(): array
    {
        return [
            'income' => 'Income',
            'expense' => 'Expense',
            'transfer' => 'Transfer',
            'fee' => 'Bank Fee',
            'interest' => 'Interest',
            'dividend' => 'Dividend',
            'refund' => 'Refund',
            'other' => 'Other',
        ];
    }

    public static function getStatistics(int $bankAccountId): array
    {
        $transactions = self::where('bank_account_id', $bankAccountId);

        return [
            'total_transactions' => $transactions->count(),
            'total_debits' => $transactions->debit()->sum('amount'),
            'total_credits' => $transactions->credit()->sum('amount'),
            'reconciled_count' => $transactions->reconciled()->count(),
            'unreconciled_count' => $transactions->unreconciled()->count(),
            'current_month_transactions' => $transactions->whereMonth('transaction_date', now()->month)->count(),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            $transaction->updateBalance();
        });

        static::updated(function ($transaction) {
            if ($transaction->isDirty(['amount', 'transaction_type'])) {
                $transaction->updateBalance();
            }
        });
    }
}
