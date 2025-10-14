<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'account_subtype',
        'parent_account_id',
        'opening_balance',
        'current_balance',
        'currency',
        'is_active',
        'description',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_account_id');
    }

    public function childAccounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_account_id');
    }

    public function subAccounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_account_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'income_account_id');
    }

    public function expenseProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'expense_account_id');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'income_account_id');
    }

    public function billItems(): HasMany
    {
        return $this->hasMany(BillItem::class, 'expense_account_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'expense_account_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeBySubtype($query, $subtype)
    {
        return $query->where('account_subtype', $subtype);
    }

    public function scopeByCurrency($query, $currency)
    {
        return $query->where('currency', $currency);
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return $this->account_code.' - '.$this->account_name;
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format((float) $this->current_balance, 2);
    }

    public function getAccountTypeTextAttribute(): string
    {
        return match ($this->account_type) {
            'asset' => __('accounting.asset'),
            'liability' => __('accounting.liability'),
            'equity' => __('accounting.equity'),
            'revenue' => __('accounting.revenue'),
            'expense' => __('accounting.expense'),
            default => $this->account_type
        };
    }

    public function getAccountSubtypeTextAttribute(): string
    {
        return match ($this->account_subtype) {
            'current_asset' => __('accounting.current_asset'),
            'fixed_asset' => __('accounting.fixed_asset'),
            'current_liability' => __('accounting.current_liability'),
            'long_term_liability' => __('accounting.long_term_liability'),
            'equity' => __('accounting.equity'),
            'operating_revenue' => __('accounting.operating_revenue'),
            'other_revenue' => __('accounting.other_revenue'),
            'operating_expense' => __('accounting.operating_expense'),
            'other_expense' => __('accounting.other_expense'),
            default => $this->account_subtype
        };
    }

    public function getBalanceTypeAttribute(): string
    {
        return in_array($this->account_type, ['asset', 'expense']) ? 'debit' : 'credit';
    }

    // Methods
    public function updateBalance(float $amount, string $type = 'debit'): void
    {
        $balanceType = $this->balance_type;

        if (($balanceType === 'debit' && $type === 'debit') ||
            ($balanceType === 'credit' && $type === 'credit')) {
            $this->current_balance += $amount;
        } else {
            $this->current_balance -= $amount;
        }

        $this->save();
    }

    public function getBalance(?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->journalEntryLines()
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'posted');
            });

        if ($startDate) {
            $query->whereHas('journalEntry', function ($q) use ($startDate) {
                $q->where('entry_date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('journalEntry', function ($q) use ($endDate) {
                $q->where('entry_date', '<=', $endDate);
            });
        }

        $debits = $query->sum('debit_amount');
        $credits = $query->sum('credit_amount');

        $balance = $this->opening_balance;

        if ($this->balance_type === 'debit') {
            $balance += ($debits - $credits);
        } else {
            $balance += ($credits - $debits);
        }

        return $balance;
    }

    public function isDebitAccount(): bool
    {
        return in_array($this->account_type, ['asset', 'expense']);
    }

    public function isCreditAccount(): bool
    {
        return in_array($this->account_type, ['liability', 'equity', 'revenue']);
    }

    public function canBeDeleted(): bool
    {
        return $this->journalEntryLines()->count() === 0 &&
               $this->products()->count() === 0 &&
               $this->expenseProducts()->count() === 0 &&
               $this->invoiceItems()->count() === 0 &&
               $this->billItems()->count() === 0 &&
               $this->expenses()->count() === 0 &&
               $this->childAccounts()->count() === 0;
    }

    public function getHierarchyLevel(): int
    {
        $level = 0;
        $parent = $this->parentAccount;

        while ($parent) {
            $level++;
            $parent = $parent->parentAccount;
        }

        return $level;
    }

    public function getFullPath(): string
    {
        $path = [$this->account_name];
        $parent = $this->parentAccount;

        while ($parent) {
            array_unshift($path, $parent->account_name);
            $parent = $parent->parentAccount;
        }

        return implode(' > ', $path);
    }

    // Static methods
    public static function getAccountTypes(): array
    {
        return [
            'asset' => __('accounting.asset'),
            'liability' => __('accounting.liability'),
            'equity' => __('accounting.equity'),
            'revenue' => __('accounting.revenue'),
            'expense' => __('accounting.expense'),
        ];
    }

    public static function getAccountSubtypes(): array
    {
        return [
            'current_asset' => __('accounting.current_asset'),
            'fixed_asset' => __('accounting.fixed_asset'),
            'current_liability' => __('accounting.current_liability'),
            'long_term_liability' => __('accounting.long_term_liability'),
            'equity' => __('accounting.equity'),
            'operating_revenue' => __('accounting.operating_revenue'),
            'other_revenue' => __('accounting.other_revenue'),
            'operating_expense' => __('accounting.operating_expense'),
            'other_expense' => __('accounting.other_expense'),
        ];
    }

    public static function generateAccountCode(string $type): string
    {
        $prefix = match ($type) {
            'asset' => '1',
            'liability' => '2',
            'equity' => '3',
            'revenue' => '4',
            'expense' => '5',
            default => '9'
        };

        $lastAccount = static::where('account_code', 'like', $prefix.'%')
            ->orderBy('account_code', 'desc')
            ->first();

        if ($lastAccount) {
            $lastNumber = (int) substr($lastAccount->account_code, 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = $prefix === '1' ? 1000 :
                        ($prefix === '2' ? 2000 :
                        ($prefix === '3' ? 3000 :
                        ($prefix === '4' ? 4000 : 5000)));
        }

        return $prefix.str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
