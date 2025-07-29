<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounting_currencies';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_base_currency',
        'is_active',
        'decimal_places',
        'symbol_position',
        'thousands_separator',
        'decimal_separator',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_base_currency' => 'boolean',
        'is_active' => 'boolean',
        'decimal_places' => 'integer',
    ];

    // Relationships
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBaseCurrency($query)
    {
        return $query->where('is_base_currency', true);
    }

    // Accessors
    public function getFormattedExchangeRateAttribute()
    {
        return number_format($this->exchange_rate, 6);
    }

    public function getSymbolPositionTextAttribute()
    {
        return $this->symbol_position === 'before' ? 'Before Amount' : 'After Amount';
    }

    // Methods
    public function formatAmount($amount)
    {
        $formattedAmount = number_format(
            $amount,
            $this->decimal_places,
            $this->decimal_separator,
            $this->thousands_separator
        );

        if ($this->symbol_position === 'before') {
            return $this->symbol . $formattedAmount;
        } else {
            return $formattedAmount . $this->symbol;
        }
    }

    public function convertToBaseCurrency($amount)
    {
        if ($this->is_base_currency) {
            return $amount;
        }

        return $amount * $this->exchange_rate;
    }

    public function convertFromBaseCurrency($amount)
    {
        if ($this->is_base_currency) {
            return $amount;
        }

        return $amount / $this->exchange_rate;
    }

    public function convertTo(Currency $targetCurrency, $amount)
    {
        // Convert to base currency first
        $baseAmount = $this->convertToBaseCurrency($amount);
        
        // Then convert to target currency
        return $targetCurrency->convertFromBaseCurrency($baseAmount);
    }

    // Static methods
    public static function getBaseCurrency()
    {
        return static::where('is_base_currency', true)->first();
    }

    public static function getActiveCurrencies()
    {
        return static::active()->orderBy('name')->get();
    }

    public static function updateExchangeRates($rates)
    {
        foreach ($rates as $code => $rate) {
            static::where('code', $code)->update(['exchange_rate' => $rate]);
        }
    }

    public static function getDefaultCurrencies()
    {
        return [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.00, 'is_base_currency' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.85],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.73],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'ر.س', 'exchange_rate' => 3.75],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'exchange_rate' => 3.67],
            ['code' => 'EGP', 'name' => 'Egyptian Pound', 'symbol' => 'ج.م', 'exchange_rate' => 30.90],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate' => 149.50],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'exchange_rate' => 1.35],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate' => 1.52],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'exchange_rate' => 0.88],
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($currency) {
            if ($currency->is_base_currency) {
                // Ensure only one base currency exists
                static::where('is_base_currency', true)->update(['is_base_currency' => false]);
            }

            // Set default values
            $currency->decimal_places = $currency->decimal_places ?? 2;
            $currency->symbol_position = $currency->symbol_position ?? 'before';
            $currency->thousands_separator = $currency->thousands_separator ?? ',';
            $currency->decimal_separator = $currency->decimal_separator ?? '.';
            $currency->is_active = $currency->is_active ?? true;
        });

        static::updating(function ($currency) {
            if ($currency->is_base_currency && $currency->isDirty('is_base_currency')) {
                // Ensure only one base currency exists
                static::where('id', '!=', $currency->id)
                      ->where('is_base_currency', true)
                      ->update(['is_base_currency' => false]);
            }
        });
    }
}
