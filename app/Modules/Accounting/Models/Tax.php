<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accounting_taxes';

    protected $fillable = [
        'name',
        'code',
        'rate',
        'type',
        'description',
        'is_active',
        'is_default',
        'country_code',
        'region',
        'applies_to',
        'calculation_method',
        'compound_tax',
        'inclusive',
        'effective_date',
        'expiry_date',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'compound_tax' => 'boolean',
        'inclusive' => 'boolean',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'applies_to' => 'array',
    ];

    // Relationships
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'accounting_invoice_taxes')
            ->withPivot('tax_amount', 'taxable_amount')
            ->withTimestamps();
    }

    public function expenses()
    {
        return $this->belongsToMany(Expense::class, 'accounting_expense_taxes')
            ->withPivot('tax_amount', 'taxable_amount')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByCountry($query, $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEffective($query, $date = null)
    {
        $date = $date ?? now();

        return $query->where(function ($q) use ($date) {
            $q->where('effective_date', '<=', $date)
                ->where(function ($subQ) use ($date) {
                    $subQ->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>=', $date);
                });
        });
    }

    // Accessors
    public function getFormattedRateAttribute()
    {
        return number_format((float) $this->rate, 2).'%';
    }

    public function getTypeTextAttribute()
    {
        $types = [
            'vat' => 'VAT',
            'gst' => 'GST',
            'sales_tax' => 'Sales Tax',
            'income_tax' => 'Income Tax',
            'withholding_tax' => 'Withholding Tax',
            'excise_tax' => 'Excise Tax',
            'customs_duty' => 'Customs Duty',
            'other' => 'Other',
        ];

        return $types[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getCalculationMethodTextAttribute()
    {
        $methods = [
            'percentage' => 'Percentage',
            'fixed_amount' => 'Fixed Amount',
            'tiered' => 'Tiered',
        ];

        return $methods[$this->calculation_method] ?? ucfirst(str_replace('_', ' ', $this->calculation_method));
    }

    public function getAppliesToTextAttribute()
    {
        if (empty($this->applies_to)) {
            return 'All Items';
        }

        $items = [
            'products' => 'Products',
            'services' => 'Services',
            'shipping' => 'Shipping',
            'digital_goods' => 'Digital Goods',
        ];

        $appliesTo = array_map(function ($item) use ($items) {
            return $items[$item] ?? ucfirst(str_replace('_', ' ', $item));
        }, $this->applies_to);

        return implode(', ', $appliesTo);
    }

    // Methods
    public function calculateTax($amount, $quantity = 1)
    {
        $taxableAmount = $amount * $quantity;

        switch ($this->calculation_method) {
            case 'percentage':
                return $this->calculatePercentageTax($taxableAmount);
            case 'fixed_amount':
                return $this->rate * $quantity;
            case 'tiered':
                return $this->calculateTieredTax($taxableAmount);
            default:
                return $this->calculatePercentageTax($taxableAmount);
        }
    }

    protected function calculatePercentageTax($amount)
    {
        if ($this->inclusive) {
            // Tax is included in the amount
            return $amount - ($amount / (1 + ($this->rate / 100)));
        } else {
            // Tax is added to the amount
            return $amount * ($this->rate / 100);
        }
    }

    protected function calculateTieredTax($amount)
    {
        // This would implement tiered tax calculation
        // For now, fallback to percentage
        return $this->calculatePercentageTax($amount);
    }

    public function isApplicableTo($itemType)
    {
        if (empty($this->applies_to)) {
            return true; // Applies to all items
        }

        return in_array($itemType, $this->applies_to);
    }

    public function isEffective($date = null)
    {
        $date = $date ?? now();

        if ($this->effective_date && $this->effective_date > $date) {
            return false;
        }

        if ($this->expiry_date && $this->expiry_date < $date) {
            return false;
        }

        return true;
    }

    // Static methods
    public static function getDefaultTax()
    {
        return static::default()->active()->first();
    }

    public static function getActiveTaxes()
    {
        return static::active()->effective()->orderBy('name')->get();
    }

    public static function getTaxesByCountry($countryCode)
    {
        return static::active()->effective()->byCountry($countryCode)->get();
    }

    public static function getDefaultTaxes()
    {
        return [
            // Saudi Arabia
            ['name' => 'VAT (Saudi Arabia)', 'code' => 'VAT_SA', 'rate' => 15.00, 'type' => 'vat', 'country_code' => 'SA', 'is_default' => true],

            // UAE
            ['name' => 'VAT (UAE)', 'code' => 'VAT_AE', 'rate' => 5.00, 'type' => 'vat', 'country_code' => 'AE'],

            // Egypt
            ['name' => 'VAT (Egypt)', 'code' => 'VAT_EG', 'rate' => 14.00, 'type' => 'vat', 'country_code' => 'EG'],

            // USA
            ['name' => 'Sales Tax (USA)', 'code' => 'SALES_US', 'rate' => 8.25, 'type' => 'sales_tax', 'country_code' => 'US'],

            // UK
            ['name' => 'VAT (UK)', 'code' => 'VAT_GB', 'rate' => 20.00, 'type' => 'vat', 'country_code' => 'GB'],

            // Canada
            ['name' => 'GST (Canada)', 'code' => 'GST_CA', 'rate' => 5.00, 'type' => 'gst', 'country_code' => 'CA'],

            // Australia
            ['name' => 'GST (Australia)', 'code' => 'GST_AU', 'rate' => 10.00, 'type' => 'gst', 'country_code' => 'AU'],

            // EU
            ['name' => 'VAT (EU Standard)', 'code' => 'VAT_EU', 'rate' => 21.00, 'type' => 'vat', 'country_code' => 'EU'],
        ];
    }

    public static function getTaxTypes()
    {
        return [
            'vat' => 'VAT',
            'gst' => 'GST',
            'sales_tax' => 'Sales Tax',
            'income_tax' => 'Income Tax',
            'withholding_tax' => 'Withholding Tax',
            'excise_tax' => 'Excise Tax',
            'customs_duty' => 'Customs Duty',
            'other' => 'Other',
        ];
    }

    public static function getCalculationMethods()
    {
        return [
            'percentage' => 'Percentage',
            'fixed_amount' => 'Fixed Amount',
            'tiered' => 'Tiered',
        ];
    }

    public static function getAppliesTo()
    {
        return [
            'products' => 'Products',
            'services' => 'Services',
            'shipping' => 'Shipping',
            'digital_goods' => 'Digital Goods',
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tax) {
            // Set default values
            $tax->calculation_method = $tax->calculation_method ?? 'percentage';
            $tax->is_active = $tax->is_active ?? true;
            $tax->compound_tax = $tax->compound_tax ?? false;
            $tax->inclusive = $tax->inclusive ?? false;
            $tax->effective_date = $tax->effective_date ?? now();
        });
    }
}
