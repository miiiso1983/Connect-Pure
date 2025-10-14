<?php

namespace App\Modules\HR\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'hr_attendance';

    protected $fillable = [
        'employee_id',
        'date',
        'scheduled_in',
        'scheduled_out',
        'actual_in',
        'actual_out',
        'break_start',
        'break_end',
        'total_hours',
        'overtime_hours',
        'late_minutes',
        'early_departure_minutes',
        'status',
        'notes',
        'location',
        'ip_address',
        'check_in_location',
        'check_out_location',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'scheduled_in' => 'datetime:H:i:s',
        'scheduled_out' => 'datetime:H:i:s',
        'actual_in' => 'datetime',
        'actual_out' => 'datetime',
        'break_start' => 'datetime:H:i:s',
        'break_end' => 'datetime:H:i:s',
        'total_hours' => 'integer',
        'overtime_hours' => 'integer',
        'late_minutes' => 'integer',
        'early_departure_minutes' => 'integer',
        'check_in_location' => 'array',
        'check_out_location' => 'array',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    // Scopes
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeCurrentMonth($query)
    {
        return $query->whereYear('date', now()->year)
            ->whereMonth('date', now()->month);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeNeedsApproval($query)
    {
        return $query->where('is_approved', false)
            ->whereIn('status', ['present', 'late', 'half_day']);
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present' => 'green',
            'late' => 'yellow',
            'absent' => 'red',
            'half_day' => 'blue',
            'on_leave' => 'purple',
            'holiday' => 'gray',
            'weekend' => 'gray',
            'sick' => 'orange',
            'excused' => 'indigo',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'present' => __('hr.present'),
            'absent' => __('hr.absent'),
            'late' => __('hr.late'),
            'half_day' => __('hr.half_day'),
            'on_leave' => __('hr.on_leave'),
            'holiday' => __('hr.holiday'),
            'weekend' => __('hr.weekend'),
            'sick' => __('hr.sick'),
            'excused' => __('hr.excused'),
            default => $this->status
        };
    }

    public function getFormattedTotalHoursAttribute(): string
    {
        if (! $this->total_hours) {
            return '0:00';
        }

        $hours = intval($this->total_hours / 60);
        $minutes = $this->total_hours % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    public function getFormattedOvertimeHoursAttribute(): string
    {
        if (! $this->overtime_hours) {
            return '0:00';
        }

        $hours = intval($this->overtime_hours / 60);
        $minutes = $this->overtime_hours % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    public function getCheckInTimeAttribute(): ?string
    {
        return $this->actual_in ? $this->actual_in->format('H:i') : null;
    }

    public function getCheckOutTimeAttribute(): ?string
    {
        return $this->actual_out ? $this->actual_out->format('H:i') : null;
    }

    public function getIsLateAttribute(): bool
    {
        if (! $this->actual_in || ! $this->scheduled_in) {
            return false;
        }

        $scheduledTime = Carbon::parse($this->date->format('Y-m-d').' '.$this->scheduled_in->format('H:i:s'));

        return $this->actual_in->gt($scheduledTime);
    }

    public function getIsEarlyDepartureAttribute(): bool
    {
        if (! $this->actual_out || ! $this->scheduled_out) {
            return false;
        }

        $scheduledTime = Carbon::parse($this->date->format('Y-m-d').' '.$this->scheduled_out->format('H:i:s'));

        return $this->actual_out->lt($scheduledTime);
    }

    public function getHasCheckedInAttribute(): bool
    {
        return ! is_null($this->actual_in);
    }

    public function getHasCheckedOutAttribute(): bool
    {
        return ! is_null($this->actual_out);
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->has_checked_in && $this->has_checked_out;
    }

    // Methods
    public function checkIn(?Carbon $time = null, ?array $location = null, ?string $ipAddress = null): void
    {
        $checkInTime = $time ?? now();

        $this->actual_in = $checkInTime;
        $this->check_in_location = $location;
        $this->ip_address = $ipAddress;

        // Calculate late minutes
        $scheduledTime = Carbon::parse($this->date->format('Y-m-d').' '.$this->scheduled_in->format('H:i:s'));
        if ($checkInTime->gt($scheduledTime)) {
            $this->late_minutes = $checkInTime->diffInMinutes($scheduledTime);
            $this->status = 'late';
        } else {
            $this->late_minutes = 0;
            $this->status = 'present';
        }

        $this->save();
    }

    public function checkOut(?Carbon $time = null, ?array $location = null): void
    {
        $checkOutTime = $time ?? now();

        $this->actual_out = $checkOutTime;
        $this->check_out_location = $location;

        // Calculate early departure minutes
        $scheduledTime = Carbon::parse($this->date->format('Y-m-d').' '.$this->scheduled_out->format('H:i:s'));
        if ($checkOutTime->lt($scheduledTime)) {
            $this->early_departure_minutes = $scheduledTime->diffInMinutes($checkOutTime);
        } else {
            $this->early_departure_minutes = 0;
        }

        // Calculate total hours and overtime
        if ($this->actual_in) {
            $this->calculateWorkingHours();
        }

        $this->save();
    }

    public function calculateWorkingHours(): void
    {
        if (! $this->actual_in || ! $this->actual_out) {
            return;
        }

        // Calculate total minutes worked
        $totalMinutes = $this->actual_out->diffInMinutes($this->actual_in);

        // Subtract break time if recorded
        if ($this->break_start && $this->break_end) {
            $breakStart = Carbon::parse($this->date->format('Y-m-d').' '.$this->break_start->format('H:i:s'));
            $breakEnd = Carbon::parse($this->date->format('Y-m-d').' '.$this->break_end->format('H:i:s'));
            $breakMinutes = $breakEnd->diffInMinutes($breakStart);
            $totalMinutes -= $breakMinutes;
        }

        $this->total_hours = $totalMinutes;

        // Calculate overtime (assuming 8 hours = 480 minutes is standard)
        $standardWorkingMinutes = 480; // 8 hours
        if ($totalMinutes > $standardWorkingMinutes) {
            $this->overtime_hours = $totalMinutes - $standardWorkingMinutes;
        } else {
            $this->overtime_hours = 0;
        }
    }

    public function approve(int $approverId): void
    {
        $this->is_approved = true;
        $this->approved_by = $approverId;
        $this->approved_at = now();
        $this->save();
    }

    public function markAsAbsent(): void
    {
        $this->status = 'absent';
        $this->actual_in = null;
        $this->actual_out = null;
        $this->total_hours = 0;
        $this->overtime_hours = 0;
        $this->late_minutes = 0;
        $this->early_departure_minutes = 0;
        $this->save();
    }

    public function markAsOnLeave(): void
    {
        $this->status = 'on_leave';
        $this->actual_in = null;
        $this->actual_out = null;
        $this->total_hours = 0;
        $this->overtime_hours = 0;
        $this->late_minutes = 0;
        $this->early_departure_minutes = 0;
        $this->save();
    }

    public function markAsHoliday(): void
    {
        $this->status = 'holiday';
        $this->actual_in = null;
        $this->actual_out = null;
        $this->total_hours = 0;
        $this->overtime_hours = 0;
        $this->late_minutes = 0;
        $this->early_departure_minutes = 0;
        $this->save();
    }

    // Static methods
    public static function getStatusOptions(): array
    {
        return [
            'present' => __('hr.present'),
            'absent' => __('hr.absent'),
            'late' => __('hr.late'),
            'half_day' => __('hr.half_day'),
            'on_leave' => __('hr.on_leave'),
            'holiday' => __('hr.holiday'),
            'weekend' => __('hr.weekend'),
            'sick' => __('hr.sick'),
            'excused' => __('hr.excused'),
        ];
    }

    public static function createDailyAttendance(?Carbon $date = null): void
    {
        $date = $date ?? now()->toDateString();

        // Skip weekends (Friday and Saturday)
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        if (in_array($dayOfWeek, [5, 6])) { // 5 = Friday, 6 = Saturday
            return;
        }

        $employees = Employee::active()->get();

        foreach ($employees as $employee) {
            // Check if attendance record already exists
            if (! static::where('employee_id', $employee->id)->where('date', $date)->exists()) {
                static::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'scheduled_in' => '09:00:00',
                    'scheduled_out' => '17:00:00',
                    'status' => 'absent', // Default to absent until check-in
                ]);
            }
        }
    }

    public static function getAttendanceStats(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = static::query();

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } else {
            $query->currentMonth();
        }

        $totalRecords = $query->count();

        return [
            'total_records' => $totalRecords,
            'present_count' => $query->clone()->present()->count(),
            'absent_count' => $query->clone()->absent()->count(),
            'late_count' => $query->clone()->late()->count(),
            'on_leave_count' => $query->clone()->byStatus('on_leave')->count(),
            'average_working_hours' => $query->clone()->present()->avg('total_hours') ?? 0,
            'total_overtime_hours' => $query->clone()->sum('overtime_hours'),
            'attendance_rate' => $totalRecords > 0 ?
                ($query->clone()->whereIn('status', ['present', 'late'])->count() / $totalRecords) * 100 : 0,
        ];
    }

    public static function getEmployeeAttendanceSummary(int $employeeId, int $year, int $month): array
    {
        $records = static::where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        return [
            'total_days' => $records->count(),
            'present_days' => $records->whereIn('status', ['present', 'late'])->count(),
            'absent_days' => $records->where('status', 'absent')->count(),
            'late_days' => $records->where('status', 'late')->count(),
            'leave_days' => $records->where('status', 'on_leave')->count(),
            'total_working_hours' => $records->sum('total_hours'),
            'total_overtime_hours' => $records->sum('overtime_hours'),
            'total_late_minutes' => $records->sum('late_minutes'),
            'attendance_rate' => $records->count() > 0 ?
                ($records->whereIn('status', ['present', 'late'])->count() / $records->count()) * 100 : 0,
        ];
    }
}
