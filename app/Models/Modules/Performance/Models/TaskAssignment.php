<?php

namespace App\Models\Modules\Performance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskAssignment extends Model
{
    protected $fillable = [
        'task_id',
        'employee_name',
        'employee_email',
        'employee_role',
        'assigned_by',
        'assigned_at',
        'started_at',
        'completed_at',
        'assignment_notes',
        'assignment_status'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->assignment_status) {
            'assigned' => 'gray',
            'accepted' => 'blue',
            'in_progress' => 'yellow',
            'completed' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->assignment_status, ['assigned', 'accepted', 'in_progress']);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->assignment_status === 'completed';
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        $diff = $this->started_at->diff($this->completed_at);

        if ($diff->days > 0) {
            return $diff->days . 'd ' . $diff->h . 'h';
        } elseif ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        } else {
            return $diff->i . 'm';
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('assignment_status', ['assigned', 'accepted', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('assignment_status', 'completed');
    }

    public function scopeByEmployee($query, $employeeName)
    {
        return $query->where('employee_name', $employeeName);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('assignment_status', $status);
    }
}
