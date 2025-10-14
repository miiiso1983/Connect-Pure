<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'accounting_invoice_items';

    protected $fillable = [
        'invoice_id',
        'item_type',
        'name',
        'description',
        'quantity',
        'unit',
        'rate',
        'amount',
        'tax_rate',
        'tax_amount',
        'discount_rate',
        'discount_amount',
        'total_amount',
        'account_id',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // Accessors
    public function getFormattedRateAttribute()
    {
        return number_format((float) $this->rate, 2);
    }

    public function getFormattedAmountAttribute()
    {
        return number_format((float) $this->amount, 2);
    }

    public function getFormattedTotalAttribute()
    {
        return number_format((float) $this->total_amount, 2);
    }

    // Methods
    public function calculateAmounts()
    {
        // Calculate base amount
        $this->amount = $this->quantity * $this->rate;

        // Calculate discount
        if ($this->discount_rate > 0) {
            $this->discount_amount = $this->amount * ($this->discount_rate / 100);
        }

        $amountAfterDiscount = $this->amount - $this->discount_amount;

        // Calculate tax
        if ($this->tax_rate > 0) {
            $this->tax_amount = $amountAfterDiscount * ($this->tax_rate / 100);
        }

        // Calculate total
        $this->total_amount = $amountAfterDiscount + $this->tax_amount;

        $this->save();
    }

    public static function getItemTypes()
    {
        return [
            'service' => __('accounting.service'),
            'product' => __('accounting.product'),
        ];
    }

    public static function getUnits()
    {
        return [
            'each' => __('accounting.each'),
            'hour' => __('accounting.hour'),
            'day' => __('accounting.day'),
            'week' => __('accounting.week'),
            'month' => __('accounting.month'),
            'year' => __('accounting.year'),
            'kg' => __('accounting.kg'),
            'lb' => __('accounting.lb'),
            'meter' => __('accounting.meter'),
            'foot' => __('accounting.foot'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateAmounts();
        });

        static::saved(function ($item) {
            $item->invoice->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->invoice->calculateTotals();
        });
    }
}
