<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $table = 'accounting_tax_rates';

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'rate',
        'type',
        'jurisdiction',
        'country',
        'state',
        'city',
        'effective_date',
        'expiry_date',
        'is_active',
        'is_compound',
        'description',
        'description_ar',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        'is_compound' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('effective_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now());
            });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByJurisdiction($query, $country, $state = null, $city = null)
    {
        $query->where('country', $country);
        
        if ($state) {
            $query->where('state', $state);
        }
        
        if ($city) {
            $query->where('city', $city);
        }
        
        return $query;
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

    public function getFormattedRateAttribute()
    {
        return number_format((float)$this->rate * 100, 2) . '%';
    }

    public function getStatusColorAttribute()
    {
        if (!$this->is_active) return 'red';
        if ($this->expiry_date && $this->expiry_date < now()) return 'orange';
        return 'green';
    }

    public function getJurisdictionTextAttribute()
    {
        $jurisdiction = $this->country;
        if ($this->state) $jurisdiction .= ', ' . $this->state;
        if ($this->city) $jurisdiction .= ', ' . $this->city;
        return $jurisdiction;
    }

    // Methods
    public function calculateTax($amount)
    {
        return $amount * $this->rate;
    }

    public function isValidForDate($date = null)
    {
        $date = $date ?: now();
        
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->effective_date > $date) {
            return false;
        }
        
        if ($this->expiry_date && $this->expiry_date < $date) {
            return false;
        }
        
        return true;
    }

    public static function getTypes()
    {
        return [
            'sales' => __('accounting.sales_tax'),
            'purchase' => __('accounting.purchase_tax'),
            'payroll' => __('accounting.payroll_tax'),
            'other' => __('accounting.other_tax'),
        ];
    }

    public static function getJurisdictions()
    {
        return [
            'Federal' => __('accounting.federal'),
            'State' => __('accounting.state'),
            'Local' => __('accounting.local'),
            'Municipal' => __('accounting.municipal'),
        ];
    }

    public static function getActiveTaxRatesForLocation($country, $state = null, $city = null, $type = 'sales')
    {
        return static::active()
            ->byType($type)
            ->byJurisdiction($country, $state, $city)
            ->orderBy('jurisdiction')
            ->get();
    }

    public static function calculateTotalTax($amount, $country, $state = null, $city = null, $type = 'sales')
    {
        $taxRates = static::getActiveTaxRatesForLocation($country, $state, $city, $type);
        $totalTax = 0;
        $taxableAmount = $amount;

        foreach ($taxRates as $taxRate) {
            $tax = $taxRate->calculateTax($taxableAmount);
            $totalTax += $tax;
            
            // If compound tax, add this tax to the taxable amount for next calculation
            if ($taxRate->is_compound) {
                $taxableAmount += $tax;
            }
        }

        return $totalTax;
    }
}
