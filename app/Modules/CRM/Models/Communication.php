<?php

namespace App\Modules\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'user_id',
        'type',
        'subject',
        'content',
        'direction',
        'status',
        'scheduled_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the contact this communication belongs to.
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the user who created this communication.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get communication type options.
     */
    public static function getTypeOptions()
    {
        return [
            'email' => 'Email',
            'phone' => 'Phone Call',
            'meeting' => 'Meeting',
            'note' => 'Note',
            'task' => 'Task',
        ];
    }

    /**
     * Get direction options.
     */
    public static function getDirectionOptions()
    {
        return [
            'inbound' => 'Inbound',
            'outbound' => 'Outbound',
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
     * Scope for pending communications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed communications.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for communications by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for communications by direction.
     */
    public function scopeByDirection($query, $direction)
    {
        return $query->where('direction', $direction);
    }

    /**
     * Scope for inbound communications.
     */
    public function scopeInbound($query)
    {
        return $query->where('direction', 'inbound');
    }

    /**
     * Scope for outbound communications.
     */
    public function scopeOutbound($query)
    {
        return $query->where('direction', 'outbound');
    }

    /**
     * Scope for recent communications.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Mark communication as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Get formatted scheduled date.
     */
    public function getFormattedScheduledDateAttribute()
    {
        if (! $this->scheduled_at) {
            return null;
        }

        return $this->scheduled_at->format('M j, Y \a\t H:i');
    }

    /**
     * Check if communication is overdue.
     */
    public function isOverdue()
    {
        return $this->status === 'pending' &&
               $this->scheduled_at &&
               $this->scheduled_at < now();
    }

    /**
     * Get type icon for UI.
     */
    public function getTypeIconAttribute()
    {
        return match ($this->type) {
            'email' => 'mail',
            'phone' => 'phone',
            'meeting' => 'calendar',
            'note' => 'document-text',
            'task' => 'clipboard-check',
            default => 'chat',
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => $this->isOverdue() ? 'red' : 'yellow',
            'completed' => 'green',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }
}
