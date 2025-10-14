<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'accounting_vendors';

    protected $fillable = [
        'vendor_number',
        'name',
        'company_name',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'tax_number',
        'currency',
        'payment_terms',
        'balance',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function recurringProfiles(): HasMany
    {
        return $this->hasMany(RecurringProfile::class);
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
        $address = $this->address;
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

    public function getTotalExpenses($year = null)
    {
        $query = $this->expenses();
        if ($year) {
            $query->whereYear('expense_date', $year);
        }

        return $query->sum('total_amount');
    }

    public function getTotalPaid($year = null)
    {
        $query = $this->payments();
        if ($year) {
            $query->whereYear('payment_date', $year);
        }

        return $query->sum('amount');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            if (! $vendor->vendor_number) {
                $vendor->vendor_number = 'VEND-'.str_pad(
                    static::max('id') + 1, 6, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
