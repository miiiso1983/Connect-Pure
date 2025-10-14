<?php

namespace App\Modules\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'user_id',
        'title',
        'description',
        'due_date',
        'priority',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the contact this follow-up belongs to.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the user assigned to this follow-up.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get priority options.
     */
    public static function getPriorityOptions()
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
        ];
    }

    /**
     * Get status options.
     */
    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    /**
     * Scope for pending follow-ups.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed follow-ups.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for cancelled follow-ups.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for overdue follow-ups.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', 'pending');
    }

    /**
     * Scope for due today.
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
            ->where('status', 'pending');
    }

    /**
     * Scope for due this week.
     */
    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'pending');
    }

    /**
     * Scope for high priority follow-ups.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope for follow-ups by contact.
     */
    public function scopeByContact($query, $contactId)
    {
        return $query->where('contact_id', $contactId);
    }

    /**
     * Scope for follow-ups by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if follow-up is overdue.
     */
    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date < now();
    }

    /**
     * Check if follow-up is due today.
     */
    public function isDueToday()
    {
        return $this->status === 'pending' && $this->due_date->isToday();
    }

    /**
     * Mark follow-up as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark follow-up as cancelled.
     */
    public function markAsCancelled()
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Get priority color for UI.
     */
    public function getPriorityColor()
    {
        return match ($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColor()
    {
        return match ($this->status) {
            'pending' => $this->isOverdue() ? 'red' : 'yellow',
            'completed' => 'green',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get formatted due date.
     */
    public function getFormattedDueDate()
    {
        if (! $this->due_date) {
            return null;
        }

        if ($this->due_date->isToday()) {
            return 'Today at '.$this->due_date->format('H:i');
        }

        if ($this->due_date->isTomorrow()) {
            return 'Tomorrow at '.$this->due_date->format('H:i');
        }

        if ($this->due_date->isYesterday()) {
            return 'Yesterday at '.$this->due_date->format('H:i');
        }

        return $this->due_date->format('M j, Y \a\t H:i');
    }
}
