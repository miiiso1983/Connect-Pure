<?php

namespace App\Models\Modules\Performance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'category',
        'created_by',
        'project_name',
        'start_date',
        'due_date',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'completion_percentage',
        'tags',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'tags' => 'array',
        'completion_percentage' => 'decimal:2'
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'on_hold' => 'yellow',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'development' => 'blue',
            'design' => 'purple',
            'testing' => 'indigo',
            'documentation' => 'gray',
            'meeting' => 'yellow',
            'research' => 'green',
            'other' => 'gray',
            default => 'gray'
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date &&
               $this->due_date < now() &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->due_date || $this->is_completed) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }

    public function getEfficiencyRateAttribute(): ?float
    {
        if (!$this->estimated_hours || !$this->actual_hours) {
            return null;
        }

        return ($this->estimated_hours / $this->actual_hours) * 100;
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->start_date || !$this->completed_at) {
            return null;
        }

        $diff = $this->start_date->diff($this->completed_at);

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
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeByEmployee($query, $employeeName)
    {
        return $query->whereHas('assignments', function ($q) use ($employeeName) {
            $q->where('employee_name', $employeeName);
        });
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByProject($query, $project)
    {
        return $query->where('project_name', $project);
    }

    public function scopeDueBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('due_date', [$startDate, $endDate]);
    }
}
