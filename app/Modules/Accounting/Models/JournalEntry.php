<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasFactory;

    protected $table = 'accounting_journal_entries';

    protected $fillable = [
        'entry_number',
        'entry_date',
        'reference',
        'description',
        'type',
        'status',
        'total_debits',
        'total_credits',
        'currency',
        'created_by',
        'posted_by',
        'posted_at',
        'reversed_by',
        'reversed_at',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debits' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    // Relationships
    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('entry_date', now()->year);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'draft' => 'gray',
            'posted' => 'green',
            'reversed' => 'red',
            default => 'gray'
        };
    }

    public function getIsBalancedAttribute()
    {
        return abs($this->total_debits - $this->total_credits) < 0.01;
    }

    public function getFormattedTotalDebitsAttribute()
    {
        return number_format((float) $this->total_debits, 2).' '.$this->currency;
    }

    public function getFormattedTotalCreditsAttribute()
    {
        return number_format((float) $this->total_credits, 2).' '.$this->currency;
    }

    // Methods
    public function calculateTotals()
    {
        $this->total_debits = $this->lines()->sum('debit_amount');
        $this->total_credits = $this->lines()->sum('credit_amount');
        $this->save();
    }

    public function post($postedBy = null)
    {
        if (! $this->is_balanced) {
            throw new \Exception(__('accounting.journal_entry_not_balanced'));
        }

        $this->status = 'posted';
        $this->posted_by = $postedBy;
        $this->posted_at = now();
        $this->save();

        // Update account balances
        foreach ($this->lines as $line) {
            if ($line->debit_amount > 0) {
                $line->account->updateBalance($line->debit_amount, 'debit');
            }
            if ($line->credit_amount > 0) {
                $line->account->updateBalance($line->credit_amount, 'credit');
            }
        }
    }

    public function reverse($reversedBy = null)
    {
        if ($this->status !== 'posted') {
            throw new \Exception(__('accounting.can_only_reverse_posted_entries'));
        }

        // Create reversing entry
        $reversingEntry = static::create([
            'entry_date' => now(),
            'reference' => 'REV-'.$this->entry_number,
            'description' => 'Reversal of '.$this->description,
            'type' => 'adjustment',
            'currency' => $this->currency,
        ]);

        // Create reversing lines
        foreach ($this->lines as $line) {
            $reversingEntry->lines()->create([
                'account_id' => $line->account_id,
                'description' => 'Reversal: '.$line->description,
                'debit_amount' => $line->credit_amount, // Swap debits and credits
                'credit_amount' => $line->debit_amount,
                'line_number' => $line->line_number,
            ]);
        }

        $reversingEntry->calculateTotals();
        $reversingEntry->post($reversedBy);

        // Mark original as reversed
        $this->status = 'reversed';
        $this->reversed_by = $reversedBy;
        $this->reversed_at = now();
        $this->save();

        return $reversingEntry;
    }

    public static function getTypes()
    {
        return [
            'manual' => __('accounting.manual'),
            'automatic' => __('accounting.automatic'),
            'adjustment' => __('accounting.adjustment'),
            'closing' => __('accounting.closing'),
        ];
    }

    public static function getStatuses()
    {
        return [
            'draft' => __('accounting.draft'),
            'posted' => __('accounting.posted'),
            'reversed' => __('accounting.reversed'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entry) {
            if (! $entry->entry_number) {
                $entry->entry_number = 'JE-'.date('Y').'-'.str_pad(
                    static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
