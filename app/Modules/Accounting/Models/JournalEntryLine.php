<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryLine extends Model
{
    use HasFactory;

    protected $table = 'accounting_journal_entry_lines';

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'description',
        'debit_amount',
        'credit_amount',
        'reference',
        'line_number',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    // Relationships
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // Accessors
    public function getFormattedDebitAmountAttribute()
    {
        return $this->debit_amount > 0 ? number_format((float) $this->debit_amount, 2) : '';
    }

    public function getFormattedCreditAmountAttribute()
    {
        return $this->credit_amount > 0 ? number_format((float) $this->credit_amount, 2) : '';
    }

    public function getAmountAttribute()
    {
        return $this->debit_amount ?: $this->credit_amount;
    }

    public function getTypeAttribute()
    {
        return $this->debit_amount > 0 ? 'debit' : 'credit';
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($line) {
            $line->journalEntry->calculateTotals();
        });

        static::deleted(function ($line) {
            $line->journalEntry->calculateTotals();
        });
    }
}
