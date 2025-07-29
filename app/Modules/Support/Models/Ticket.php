<?php

namespace App\Modules\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'status',
        'priority',
        'category',
        'assigned_to',
        'created_by',
        'resolved_at',
        'resolution_notes',
        'customer_email',
        'customer_name',
        'customer_phone',
        'tags',
        'due_date',
    ];

    protected $casts = [
        'tags' => 'array',
        'resolved_at' => 'datetime',
        'due_date' => 'datetime',
        'satisfaction_rating' => 'integer',
    ];

    protected $dates = [
        'resolved_at',
        'due_date',
        'created_at',
        'updated_at',
    ];

    // Relationships
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for overdue tickets.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope for pending tickets.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for urgent tickets.
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Scope for unassigned tickets.
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    /**
     * Scope for tickets due today.
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
                    ->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope for tickets due this week.
     */
    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope for recent tickets.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for tickets by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for tickets by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for active tickets (not resolved or closed).
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['resolved', 'closed']);
    }

    // Accessors & Mutators
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'open' => 'red',
            'in_progress' => 'yellow',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
            'urgent' => 'purple',
            default => 'gray',
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Check if ticket is active (not resolved or closed).
     */
    public function isActive()
    {
        return !in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Check if ticket is assigned.
     */
    public function isAssigned()
    {
        return !empty($this->assigned_to);
    }

    /**
     * Check if ticket is due today.
     */
    public function isDueToday()
    {
        return $this->due_date && $this->due_date->isToday() && $this->isActive();
    }



    public function getResponseTimeAttribute()
    {
        $firstComment = $this->comments()->where('created_by', '!=', $this->created_by)->first();
        
        if (!$firstComment) {
            return null;
        }

        return $this->created_at->diffInMinutes($firstComment->created_at);
    }

    public function getResolutionTimeAttribute()
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->created_at->diffInMinutes($this->resolved_at);
    }

    // Methods
    public function addComment(string $comment, int $userId, array $attachments = []): TicketComment
    {
        $ticketComment = $this->comments()->create([
            'comment' => $comment,
            'created_by' => $userId,
        ]);

        // Add attachments if provided
        foreach ($attachments as $attachment) {
            $ticketComment->attachments()->create($attachment);
        }

        return $ticketComment;
    }

    public function assignTo(int $userId): void
    {
        $this->update(['assigned_to' => $userId]);
    }

    public function resolve(string $resolutionNotes, int $userId): void
    {
        $this->update([
            'status' => 'resolved',
            'resolution_notes' => $resolutionNotes,
            'resolved_at' => now(),
        ]);

        $this->addComment("Ticket resolved: {$resolutionNotes}", $userId);
    }

    public function reopen(int $userId): void
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
            'resolution_notes' => null,
        ]);

        $this->addComment('Ticket reopened', $userId);
    }

    public function close(int $userId): void
    {
        $this->update(['status' => 'closed']);
        $this->addComment('Ticket closed', $userId);
    }

    public function updatePriority(string $priority, int $userId): void
    {
        $oldPriority = $this->priority;
        $this->update(['priority' => $priority]);
        
        $this->addComment("Priority changed from {$oldPriority} to {$priority}", $userId);
    }

    public function addSatisfactionRating(int $rating, ?string $feedback = null): void
    {
        $this->update([
            'satisfaction_rating' => $rating,
            'satisfaction_feedback' => $feedback,
        ]);
    }

    /**
     * Generate unique ticket number.
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');

        // Get the last ticket number for today
        $lastTicket = static::where('ticket_number', 'like', "{$prefix}-{$date}-%")
                           ->orderBy('ticket_number', 'desc')
                           ->first();

        if ($lastTicket) {
            // Extract the sequence number and increment
            $lastNumber = (int) substr($lastTicket->ticket_number, -4);
            $sequence = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // First ticket of the day
            $sequence = '0001';
        }

        return "{$prefix}-{$date}-{$sequence}";
    }

    /**
     * Boot method to auto-generate ticket number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
        });
    }

    // Static methods
    public static function getStatusOptions(): array
    {
        return [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];
    }

    public static function getPriorityOptions(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    public static function getCategoryOptions(): array
    {
        return [
            'technical' => 'Technical Issue',
            'billing' => 'Billing',
            'feature_request' => 'Feature Request',
            'bug_report' => 'Bug Report',
            'general' => 'General Inquiry',
            'account' => 'Account Issue',
            'other' => 'Other',
        ];
    }

    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'open' => self::open()->count(),
            'in_progress' => self::inProgress()->count(),
            'resolved' => self::resolved()->count(),
            'closed' => self::closed()->count(),
            'high_priority' => self::highPriority()->count(),
            'overdue' => self::whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count(),
        ];
    }
}
