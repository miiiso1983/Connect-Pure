<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class RecurringProfile extends Model
{
    use HasFactory;

    protected $table = 'accounting_recurring_profiles';

    protected $fillable = [
        'profile_name',
        'type',
        'customer_id',
        'vendor_id',
        'frequency',
        'interval',
        'start_date',
        'end_date',
        'max_occurrences',
        'occurrences_created',
        'next_run_date',
        'last_run_date',
        'status',
        'currency',
        'amount',
        'description',
        'template_data',
        'auto_send',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_date' => 'date',
        'last_run_date' => 'date',
        'amount' => 'decimal:2',
        'template_data' => 'array',
        'auto_send' => 'boolean',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDueForProcessing($query)
    {
        return $query->where('status', 'active')
            ->where('next_run_date', '<=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'paused' => 'yellow',
            'completed' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getFormattedAmountAttribute()
    {
        return number_format((float) $this->amount, 2) . ' ' . $this->currency;
    }

    public function getFrequencyTextAttribute()
    {
        $frequency = match($this->frequency) {
            'weekly' => __('accounting.weekly'),
            'bi_weekly' => __('accounting.bi_weekly'),
            'monthly' => __('accounting.monthly'),
            'quarterly' => __('accounting.quarterly'),
            'semi_annually' => __('accounting.semi_annually'),
            'annually' => __('accounting.annually'),
            default => $this->frequency
        };

        return $this->interval > 1 ? 
            __('accounting.every_x_frequency', ['interval' => $this->interval, 'frequency' => $frequency]) :
            $frequency;
    }

    public function getNextRunDateFormattedAttribute()
    {
        return $this->next_run_date ? $this->next_run_date->format('M j, Y') : null;
    }

    public function getRemainingOccurrencesAttribute()
    {
        if (!$this->max_occurrences) {
            return null; // Unlimited
        }
        return max(0, $this->max_occurrences - $this->occurrences_created);
    }

    // Methods
    public function calculateNextRunDate()
    {
        $baseDate = \Carbon\Carbon::parse($this->last_run_date ?: $this->start_date);

        $nextDate = match($this->frequency) {
            'weekly' => $baseDate->addWeeks($this->interval),
            'bi_weekly' => $baseDate->addWeeks(2 * $this->interval),
            'monthly' => $baseDate->addMonths($this->interval),
            'quarterly' => $baseDate->addMonths(3 * $this->interval),
            'semi_annually' => $baseDate->addMonths(6 * $this->interval),
            'annually' => $baseDate->addYears($this->interval),
            default => $baseDate->addDays(1)
        };

        $this->next_run_date = $nextDate;
        $this->save();

        return $nextDate;
    }

    public function shouldProcess()
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->next_run_date > now()) {
            return false;
        }

        if ($this->end_date && $this->end_date < now()) {
            $this->status = 'completed';
            $this->save();
            return false;
        }

        if ($this->max_occurrences && $this->occurrences_created >= $this->max_occurrences) {
            $this->status = 'completed';
            $this->save();
            return false;
        }

        return true;
    }

    public function process()
    {
        if (!$this->shouldProcess()) {
            return null;
        }

        $created = null;

        switch ($this->type) {
            case 'invoice':
                $created = $this->createInvoice();
                break;
            case 'expense':
                $created = $this->createExpense();
                break;
            case 'payment':
                $created = $this->createPayment();
                break;
        }

        if ($created) {
            $this->occurrences_created++;
            $this->last_run_date = now();
            $this->calculateNextRunDate();
        }

        return $created;
    }

    private function createInvoice()
    {
        $templateData = $this->template_data;
        
        $invoice = Invoice::create([
            'customer_id' => $this->customer_id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30), // Default 30 days
            'currency' => $this->currency,
            'total_amount' => $this->amount,
            'balance_due' => $this->amount,
            'is_recurring' => true,
            'recurring_profile_id' => $this->id,
            'notes' => $this->description,
        ]);

        // Add items from template
        if (isset($templateData['items'])) {
            foreach ($templateData['items'] as $itemData) {
                $invoice->items()->create($itemData);
            }
            $invoice->calculateTotals();
        }

        if ($this->auto_send) {
            $invoice->markAsSent();
        }

        return $invoice;
    }

    private function createExpense()
    {
        return Expense::create([
            'vendor_id' => $this->vendor_id,
            'expense_date' => now(),
            'description' => $this->description,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'total_amount' => $this->amount,
            'is_recurring' => true,
        ]);
    }

    private function createPayment()
    {
        return Payment::create([
            'type' => $this->customer_id ? 'customer_payment' : 'vendor_payment',
            'customer_id' => $this->customer_id,
            'vendor_id' => $this->vendor_id,
            'payment_date' => now(),
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => 'pending',
        ]);
    }

    public function pause()
    {
        $this->status = 'paused';
        $this->save();
    }

    public function resume()
    {
        $this->status = 'active';
        $this->save();
    }

    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public static function getTypes()
    {
        return [
            'invoice' => __('accounting.invoice'),
            'expense' => __('accounting.expense'),
            'payment' => __('accounting.payment'),
        ];
    }

    public static function getFrequencies()
    {
        return [
            'weekly' => __('accounting.weekly'),
            'bi_weekly' => __('accounting.bi_weekly'),
            'monthly' => __('accounting.monthly'),
            'quarterly' => __('accounting.quarterly'),
            'semi_annually' => __('accounting.semi_annually'),
            'annually' => __('accounting.annually'),
        ];
    }

    public static function getStatuses()
    {
        return [
            'active' => __('accounting.active'),
            'paused' => __('accounting.paused'),
            'completed' => __('accounting.completed'),
            'cancelled' => __('accounting.cancelled'),
        ];
    }
}
