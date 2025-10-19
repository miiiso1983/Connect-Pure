<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'accounting_customers';

    protected $fillable = [
        'customer_number',
        'name',
        'company_name',
        'email',
        'phone',
        'whatsapp_number',
        'website',
        'billing_address',
        'shipping_address',
        'city',
        'state',
        'postal_code',
        'country',
        'tax_number',
        'currency',
        'payment_terms',
        'credit_limit',
        'balance',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function recurringProfiles(): HasMany
    {
        return $this->hasMany(RecurringProfile::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return $this->company_name ?: $this->name;
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format((float) $this->balance, 2).' '.$this->currency;
    }

    public function getFullAddressAttribute()
    {
        $address = $this->billing_address;
        if ($this->city) {
            $address .= ', '.$this->city;
        }
        if ($this->state) {
            $address .= ', '.$this->state;
        }
        if ($this->postal_code) {
            $address .= ' '.$this->postal_code;
        }
        if ($this->country) {
            $address .= ', '.$this->country;
        }

        return $address;
    }

    public function getStatusColorAttribute()
    {
        if (! $this->is_active) {
            return 'red';
        }
        if ($this->balance > 0) {
            return 'orange';
        }

        return 'green';
    }

    // Methods
    public function updateBalance($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    public function getTotalInvoiced($year = null)
    {
        $query = $this->invoices();
        if ($year) {
            $query->whereYear('invoice_date', $year);
        }

        return $query->sum('total_amount');
    }

    public function getTotalPaid($year = null)
    {
        $query = $this->invoices();
        if ($year) {
            $query->whereYear('invoice_date', $year);
        }

        return $query->sum('paid_amount');
    }

    public function getOverdueInvoices()
    {
        return $this->invoices()
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->get();
    }

    public static function getPaymentTerms()
    {
        return [
            'net_15' => __('accounting.net_15'),
            'net_30' => __('accounting.net_30'),
            'net_45' => __('accounting.net_45'),
            'net_60' => __('accounting.net_60'),
            'due_on_receipt' => __('accounting.due_on_receipt'),
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (! $customer->customer_number) {
                $customer->customer_number = 'CUST-'.str_pad(
                    (string) (static::max('id') + 1), 6, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
