<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    use HasFactory;

    protected $table = 'accounting_bill_items';

    protected $fillable = [
        'bill_id',
        'product_id',
        'expense_account_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
        'tax_rate_id',
        'tax_amount',
        'total_amount',
        'discount_rate',
        'discount_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    // Relationships
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'expense_account_id');
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    // Accessors & Mutators
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getDiscountedAmountAttribute()
    {
        $subtotal = $this->subtotal;

        return $subtotal - $this->discount_amount;
    }

    public function getTotalWithTaxAttribute()
    {
        return $this->discounted_amount + $this->tax_amount;
    }

    // Methods
    public function calculateAmounts(): void
    {
        // Calculate base amount
        $this->amount = $this->quantity * $this->unit_price;

        // Calculate discount
        if ($this->discount_rate > 0) {
            $this->discount_amount = $this->amount * ($this->discount_rate / 100);
        }

        $amountAfterDiscount = $this->amount - $this->discount_amount;

        // Calculate tax
        if ($this->taxRate) {
            $this->tax_amount = $amountAfterDiscount * ($this->taxRate->rate / 100);
        }

        // Calculate total
        $this->total_amount = $amountAfterDiscount + $this->tax_amount;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($billItem) {
            $billItem->calculateAmounts();
        });
    }
}
