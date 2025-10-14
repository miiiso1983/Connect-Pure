<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounting_accounts';

    protected $fillable = [
        'account_number',
        'name',
        'name_ar',
        'type',
        'subtype',
        'description',
        'description_ar',
        'balance',
        'currency',
        'is_active',
        'is_system',
        'parent_id',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySubtype($query, $subtype)
    {
        return $query->where('subtype', $subtype);
    }

    // Accessors
    public function getLocalizedNameAttribute()
    {
        return app()->getLocale() === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }

    public function getLocalizedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format((float) $this->balance, 2).' '.$this->currency;
    }

    // Methods
    public function updateBalance($amount, $type = 'debit')
    {
        if ($type === 'debit') {
            if (in_array($this->type, ['asset', 'expense'])) {
                $this->balance = (float) $this->balance + $amount;
            } else {
                $this->balance = (float) $this->balance - $amount;
            }
        } else { // credit
            if (in_array($this->type, ['liability', 'equity', 'revenue'])) {
                $this->balance = (float) $this->balance + $amount;
            } else {
                $this->balance = (float) $this->balance - $amount;
            }
        }
        $this->save();
    }

    public function getAccountTypeColor()
    {
        return match ($this->type) {
            'asset' => 'green',
            'liability' => 'red',
            'equity' => 'blue',
            'revenue' => 'purple',
            'expense' => 'orange',
            default => 'gray'
        };
    }

    public static function getAccountTypes()
    {
        return [
            'asset' => __('accounting.asset'),
            'liability' => __('accounting.liability'),
            'equity' => __('accounting.equity'),
            'revenue' => __('accounting.revenue'),
            'expense' => __('accounting.expense'),
        ];
    }

    public static function getAccountSubtypes()
    {
        return [
            'current_asset' => __('accounting.current_asset'),
            'fixed_asset' => __('accounting.fixed_asset'),
            'other_asset' => __('accounting.other_asset'),
            'current_liability' => __('accounting.current_liability'),
            'long_term_liability' => __('accounting.long_term_liability'),
            'equity' => __('accounting.equity'),
            'income' => __('accounting.income'),
            'other_income' => __('accounting.other_income'),
            'cost_of_goods_sold' => __('accounting.cost_of_goods_sold'),
            'expense' => __('accounting.expense'),
            'other_expense' => __('accounting.other_expense'),
        ];
    }
}
