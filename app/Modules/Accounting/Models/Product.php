<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'type',
        'category',
        'unit_price',
        'cost_price',
        'unit_of_measure',
        'quantity_on_hand',
        'reorder_point',
        'income_account_id',
        'expense_account_id',
        'tax_rate',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'quantity_on_hand' => 'integer',
        'reorder_point' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function incomeAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'income_account_id');
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'expense_account_id');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function billItems(): HasMany
    {
        return $this->hasMany(BillItem::class);
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

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeLowStock($query)
    {
        return $query->where('type', 'product')
            ->whereColumn('quantity_on_hand', '<=', 'reorder_point');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->unit_price, 2);
    }

    public function getFormattedCostAttribute(): string
    {
        return number_format((float) $this->cost_price ?? 0, 2);
    }

    public function getTypeTextAttribute(): string
    {
        return match ($this->type) {
            'product' => __('accounting.product'),
            'service' => __('accounting.service'),
            default => $this->type
        };
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->type === 'service') {
            return 'n/a';
        }

        if ($this->quantity_on_hand <= 0) {
            return 'out_of_stock';
        } elseif ($this->quantity_on_hand <= $this->reorder_point) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'red',
            'low_stock' => 'yellow',
            'in_stock' => 'green',
            default => 'gray'
        };
    }

    public function getStockStatusTextAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => __('accounting.out_of_stock'),
            'low_stock' => __('accounting.low_stock'),
            'in_stock' => __('accounting.in_stock'),
            'n/a' => __('accounting.not_applicable'),
            default => $this->stock_status
        };
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price <= 0) {
            return 0;
        }

        return (($this->unit_price - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getInventoryValueAttribute(): float
    {
        return $this->quantity_on_hand * ($this->cost_price ?? 0);
    }

    // Methods
    public function adjustStock(int $quantity, ?string $reason = null): void
    {
        $this->quantity_on_hand += $quantity;
        $this->save();

        // Log the adjustment if reason is provided
        if ($reason) {
            // Could log to stock adjustment table
        }

        // Log stock adjustment if needed
        // StockAdjustment::create([...]);
    }

    public function updateStock(int $newQuantity): void
    {
        $this->quantity_on_hand = $newQuantity;
        $this->save();
    }

    public function isLowStock(): bool
    {
        return $this->type === 'product' &&
               $this->quantity_on_hand <= $this->reorder_point;
    }

    public function isOutOfStock(): bool
    {
        return $this->type === 'product' && $this->quantity_on_hand <= 0;
    }

    public function canBeSold(): bool
    {
        return $this->is_active &&
               ($this->type === 'service' || $this->quantity_on_hand > 0);
    }

    public function getTotalSold(?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->invoiceItems()
            ->whereHas('invoice', function ($q) {
                $q->whereIn('status', ['sent', 'viewed', 'partial', 'paid']);
            });

        if ($startDate) {
            $query->whereHas('invoice', function ($q) use ($startDate) {
                $q->where('invoice_date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('invoice', function ($q) use ($endDate) {
                $q->where('invoice_date', '<=', $endDate);
            });
        }

        return $query->sum('quantity');
    }

    public function getTotalRevenue(?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->invoiceItems()
            ->whereHas('invoice', function ($q) {
                $q->whereIn('status', ['sent', 'viewed', 'partial', 'paid']);
            });

        if ($startDate) {
            $query->whereHas('invoice', function ($q) use ($startDate) {
                $q->where('invoice_date', '>=', $startDate);
            });
        }

        if ($endDate) {
            $query->whereHas('invoice', function ($q) use ($endDate) {
                $q->where('invoice_date', '<=', $endDate);
            });
        }

        return $query->sum('line_total');
    }

    public function duplicate(): Product
    {
        $newProduct = $this->replicate();
        $newProduct->sku = static::generateSKU();
        $newProduct->name = $this->name.' (Copy)';
        $newProduct->save();

        return $newProduct;
    }

    // Static methods
    public static function generateSKU(): string
    {
        $lastProduct = static::orderBy('sku', 'desc')->first();

        if ($lastProduct && preg_match('/SKU(\d+)/', $lastProduct->sku, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1001;
        }

        return 'SKU'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function getTypeOptions(): array
    {
        return [
            'product' => __('accounting.product'),
            'service' => __('accounting.service'),
        ];
    }

    public static function getUnitOfMeasureOptions(): array
    {
        return [
            'each' => __('accounting.each'),
            'hour' => __('accounting.hour'),
            'day' => __('accounting.day'),
            'month' => __('accounting.month'),
            'kg' => __('accounting.kilogram'),
            'lb' => __('accounting.pound'),
            'meter' => __('accounting.meter'),
            'foot' => __('accounting.foot'),
            'liter' => __('accounting.liter'),
            'gallon' => __('accounting.gallon'),
        ];
    }

    public static function getCategoryOptions(): array
    {
        return static::distinct('category')
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();
    }

    public static function getLowStockProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return static::lowStock()->get();
    }

    public static function getTopSellingProducts(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return static::withSum('invoiceItems', 'quantity')
            ->orderBy('invoice_items_sum_quantity', 'desc')
            ->limit($limit)
            ->get();
    }
}
