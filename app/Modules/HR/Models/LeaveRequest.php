<?php

namespace App\Modules\HR\Models;

use Carbon\Carbon;
use Database\Factories\HR\LeaveRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return LeaveRequestFactory::new();
    }

    protected $table = 'hr_leave_requests';

    protected $fillable = [
        'request_number',
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'is_half_day',
        'half_day_period',
        'reason',
        'reason_ar',
        'contact_during_leave',
        'attachments',
        'status',
        'approver_id',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'is_paid',
        'deduction_amount',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'is_half_day' => 'boolean',
        'attachments' => 'array',
        'approved_at' => 'datetime',
        'is_paid' => 'boolean',
        'deduction_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'processed_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByLeaveType($query, $type)
    {
        return $query->where('leave_type', $type);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($subQ) use ($startDate, $endDate) {
                    $subQ->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }

    public function scopeCurrentYear($query)
    {
        return $query->whereYear('start_date', now()->year);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'pending' => __('hr.pending'),
            'approved' => __('hr.approved'),
            'rejected' => __('hr.rejected'),
            'cancelled' => __('hr.cancelled'),
            default => $this->status
        };
    }

    public function getLeaveTypeTextAttribute(): string
    {
        return match ($this->leave_type) {
            'annual' => __('hr.annual_leave'),
            'sick' => __('hr.sick_leave'),
            'emergency' => __('hr.emergency_leave'),
            'maternity' => __('hr.maternity_leave'),
            'paternity' => __('hr.paternity_leave'),
            'unpaid' => __('hr.unpaid_leave'),
            'study' => __('hr.study_leave'),
            'hajj' => __('hr.hajj_leave'),
            'bereavement' => __('hr.bereavement_leave'),
            'other' => __('hr.other'),
            default => $this->leave_type
        };
    }

    public function getDisplayReasonAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->reason_ar
            ? $this->reason_ar
            : $this->reason;
    }

    public function getDurationTextAttribute(): string
    {
        if ($this->is_half_day) {
            $period = $this->half_day_period === 'morning' ? __('hr.morning') : __('hr.afternoon');

            return __('hr.half_day_period', ['period' => $period]);
        }

        if ($this->total_days == 1) {
            return __('hr.one_day');
        }

        return __('hr.days_count', ['count' => $this->total_days]);
    }

    public function getFormattedDateRangeAttribute(): string
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('Y-m-d');
        }

        return $this->start_date->format('Y-m-d').' - '.$this->end_date->format('Y-m-d');
    }

    public function getIsOverlapAttribute(): bool
    {
        return static::where('employee_id', $this->employee_id)
            ->where('id', '!=', $this->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) {
                $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                    ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);
                    });
            })
            ->exists();
    }

    public function getCanBeApprovedAttribute(): bool
    {
        return $this->status === 'pending' && ! $this->is_overlap;
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, ['pending', 'approved']) && $this->start_date > now();
    }

    // Methods
    public function calculateTotalDays(): int
    {
        if ($this->is_half_day) {
            return 1; // Half day counts as 1 day for calculation
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        // Calculate business days (excluding weekends)
        $totalDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Skip weekends (Friday and Saturday in Saudi Arabia)
            if (! in_array($currentDate->dayOfWeek, [5, 6])) { // 5 = Friday, 6 = Saturday
                $totalDays++;
            }
            $currentDate->addDay();
        }

        return $totalDays;
    }

    public function approve(int $approverId, ?string $notes = null): void
    {
        $this->status = 'approved';
        $this->approver_id = $approverId;
        $this->approved_at = now();
        $this->approval_notes = $notes;
        $this->save();

        // Deduct leave balance if it's a paid leave type
        if (in_array($this->leave_type, ['annual', 'sick', 'emergency'])) {
            $this->employee->deductLeaveBalance($this->leave_type, $this->total_days);
        }
    }

    public function reject(int $approverId, string $reason): void
    {
        $this->status = 'rejected';
        $this->approver_id = $approverId;
        $this->approved_at = now();
        $this->rejection_reason = $reason;
        $this->save();
    }

    public function cancel(): void
    {
        // Restore leave balance if it was already deducted
        if ($this->status === 'approved' && in_array($this->leave_type, ['annual', 'sick', 'emergency'])) {
            $this->employee->restoreLeaveBalance($this->leave_type, $this->total_days);
        }

        $this->status = 'cancelled';
        $this->save();
    }

    public function process(int $processorId): void
    {
        $this->processed_by = $processorId;
        $this->processed_at = now();
        $this->save();
    }

    public function hasAttachments(): bool
    {
        return ! empty($this->attachments);
    }

    public function getAttachmentsList(): array
    {
        return $this->attachments ?? [];
    }

    // Static methods
    public static function generateRequestNumber(): string
    {
        $lastRequest = static::orderBy('request_number', 'desc')->first();

        if ($lastRequest && preg_match('/LR(\d+)/', $lastRequest->request_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1001;
        }

        return 'LR'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function getLeaveTypeOptions(): array
    {
        return [
            'annual' => __('hr.annual_leave'),
            'sick' => __('hr.sick_leave'),
            'emergency' => __('hr.emergency_leave'),
            'maternity' => __('hr.maternity_leave'),
            'paternity' => __('hr.paternity_leave'),
            'unpaid' => __('hr.unpaid_leave'),
            'study' => __('hr.study_leave'),
            'hajj' => __('hr.hajj_leave'),
            'bereavement' => __('hr.bereavement_leave'),
            'other' => __('hr.other'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => __('hr.pending'),
            'approved' => __('hr.approved'),
            'rejected' => __('hr.rejected'),
            'cancelled' => __('hr.cancelled'),
        ];
    }

    public static function getLeaveStats(): array
    {
        return [
            'total_requests' => static::count(),
            'pending_requests' => static::pending()->count(),
            'approved_requests' => static::approved()->count(),
            'rejected_requests' => static::byStatus('rejected')->count(),
            'requests_by_type' => static::selectRaw('leave_type, COUNT(*) as count')
                ->groupBy('leave_type')
                ->pluck('count', 'leave_type')
                ->toArray(),
            'average_leave_days' => static::approved()->avg('total_days') ?? 0,
        ];
    }

    public static function getEmployeeLeaveHistory(int $employeeId, ?int $year = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::where('employee_id', $employeeId)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('start_date', 'desc');

        if ($year) {
            $query->whereYear('start_date', $year);
        }

        return $query->get();
    }
}
