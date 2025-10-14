<?php

namespace App\Models\Modules\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'status',
        'priority',
        'category',
        'customer_name',
        'customer_email',
        'customer_phone',
        'assigned_to',
        'created_by',
        'due_date',
        'resolved_at',
        'resolution_notes',
        'tags',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
        'tags' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-'.strtoupper(uniqid());
            }
        });
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at', 'asc');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->orderBy('created_at', 'desc');
    }

    public function publicComments(): HasMany
    {
        return $this->hasMany(TicketComment::class)
            ->where('is_internal', false)
            ->orderBy('created_at', 'asc');
    }

    public function internalComments(): HasMany
    {
        return $this->hasMany(TicketComment::class)
            ->where('is_internal', true)
            ->orderBy('created_at', 'asc');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'blue',
            'in_progress' => 'yellow',
            'pending' => 'orange',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'technical' => 'blue',
            'billing' => 'purple',
            'general' => 'gray',
            'feature_request' => 'indigo',
            'bug_report' => 'red',
            default => 'gray'
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date < now() && ! in_array($this->status, ['resolved', 'closed']);
    }

    public function getResponseTimeAttribute(): ?string
    {
        $firstResponse = $this->comments()
            ->where('author_type', '!=', 'customer')
            ->first();

        if (! $firstResponse) {
            return null;
        }

        $diff = $this->created_at->diff($firstResponse->created_at);

        if ($diff->days > 0) {
            return $diff->days.'d '.$diff->h.'h';
        } elseif ($diff->h > 0) {
            return $diff->h.'h '.$diff->i.'m';
        } else {
            return $diff->i.'m';
        }
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'pending']);
    }

    public function scopeAssignedTo($query, $assignee)
    {
        return $query->where('assigned_to', $assignee);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', ['resolved', 'closed']);
    }
}
