<?php

namespace App\Modules\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'position',
        'status',
        'priority',
        'city',
        'country',
        'notes',
        'source',
        'assigned_to',
        'type',
        'potential_value',
        'lead_score',
        'last_contact_date',
        'next_follow_up_date',
        'tags',
    ];

    protected $casts = [
        'potential_value' => 'decimal:2',
        'lead_score' => 'integer',
        'last_contact_date' => 'date',
        'next_follow_up_date' => 'date',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user assigned to this contact.
     */
    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    /**
     * Get all communications for this contact.
     */
    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    /**
     * Get all follow-ups for this contact.
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get pending follow-ups for this contact.
     */
    public function pendingFollowUps()
    {
        return $this->hasMany(FollowUp::class)->where('status', 'pending');
    }

    /**
     * Get completed follow-ups for this contact.
     */
    public function completedFollowUps()
    {
        return $this->hasMany(FollowUp::class)->where('status', 'completed');
    }

    /**
     * Get overdue follow-ups for this contact.
     */
    public function overdueFollowUps()
    {
        return $this->hasMany(FollowUp::class)
            ->where('status', 'pending')
            ->where('due_date', '<', now());
    }

    /**
     * Get today's follow-ups for this contact.
     */
    public function todayFollowUps()
    {
        return $this->hasMany(FollowUp::class)
            ->where('status', 'pending')
            ->whereDate('due_date', today());
    }

    /**
     * Get recent communications for this contact.
     */
    public function recentCommunications()
    {
        return $this->hasMany(Communication::class)
            ->orderBy('created_at', 'desc')
            ->limit(5);
    }

    /**
     * Scope for searching contacts.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('company', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for leads.
     */
    public function scopeLeads($query)
    {
        return $query->where('type', 'lead');
    }

    /**
     * Scope for clients.
     */
    public function scopeClients($query)
    {
        return $query->where('type', 'client');
    }

    /**
     * Scope for prospects.
     */
    public function scopeProspects($query)
    {
        return $query->where('type', 'prospect');
    }

    /**
     * Scope for hot leads.
     */
    public function scopeHotLeads($query)
    {
        return $query->where('priority', 'high')
            ->where('type', 'lead');
    }

    /**
     * Scope for qualified leads.
     */
    public function scopeQualified($query)
    {
        return $query->where('status', 'qualified');
    }

    /**
     * Scope for closed won deals.
     */
    public function scopeClosedWon($query)
    {
        return $query->where('status', 'closed_won');
    }

    /**
     * Scope for closed lost deals.
     */
    public function scopeClosedLost($query)
    {
        return $query->where('status', 'closed_lost');
    }

    /**
     * Scope for active pipeline.
     */
    public function scopeActivePipeline($query)
    {
        return $query->whereIn('status', ['qualified', 'proposal', 'negotiation']);
    }

    /**
     * Scope for filtering by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for filtering by source.
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Get type options.
     */
    public static function getTypeOptions()
    {
        return [
            'lead' => 'Lead',
            'prospect' => 'Prospect',
            'client' => 'Client',
            'partner' => 'Partner',
        ];
    }

    /**
     * Get status options.
     */
    public static function getStatusOptions()
    {
        return [
            'new' => 'New',
            'contacted' => 'Contacted',
            'qualified' => 'Qualified',
            'proposal' => 'Proposal Sent',
            'negotiation' => 'In Negotiation',
            'closed_won' => 'Closed Won',
            'closed_lost' => 'Closed Lost',
            'inactive' => 'Inactive',
        ];
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
     * Get source options.
     */
    public static function getSourceOptions()
    {
        return [
            'website' => 'Website',
            'social_media' => 'Social Media',
            'referral' => 'Referral',
            'advertising' => 'Advertising',
            'cold_call' => 'Cold Call',
            'email' => 'Email',
            'event' => 'Event',
            'other' => 'Other',
        ];
    }

    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'new' => 'blue',
            'contacted' => 'indigo',
            'qualified' => 'green',
            'proposal' => 'purple',
            'negotiation' => 'orange',
            'closed_won' => 'green',
            'closed_lost' => 'red',
            'inactive' => 'gray',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get priority color for display.
     */
    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
        ];

        return $colors[$this->priority] ?? 'gray';
    }

    /**
     * Get the next follow-up for this contact.
     */
    public function getNextFollowUpAttribute()
    {
        return $this->pendingFollowUps()
            ->orderBy('due_date', 'asc')
            ->first();
    }

    /**
     * Get the last communication for this contact.
     */
    public function getLastCommunicationAttribute()
    {
        return $this->communications()
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Check if contact has overdue follow-ups.
     */
    public function hasOverdueFollowUps()
    {
        return $this->overdueFollowUps()->exists();
    }

    /**
     * Check if contact has follow-ups due today.
     */
    public function hasFollowUpsDueToday()
    {
        return $this->todayFollowUps()->exists();
    }

    /**
     * Get contact's activity score (based on communications and follow-ups).
     */
    public function getActivityScore()
    {
        $communicationsCount = $this->communications()->count();
        $followUpsCount = $this->followUps()->count();
        $completedFollowUps = $this->completedFollowUps()->count();

        return ($communicationsCount * 2) + ($followUpsCount * 1) + ($completedFollowUps * 3);
    }

    /**
     * Mark contact as converted (lead to client).
     */
    public function markAsConverted()
    {
        $this->update([
            'type' => 'client',
            'status' => 'closed_won',
        ]);
    }

    /**
     * Get days since last contact.
     */
    public function getDaysSinceLastContact()
    {
        if (! $this->last_contact_date) {
            return null;
        }

        return now()->diffInDays($this->last_contact_date);
    }

    /**
     * Update last contact date.
     */
    public function updateLastContactDate()
    {
        $this->update(['last_contact_date' => now()]);
    }

    /**
     * Get formatted display name.
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->name;
        if ($this->company) {
            $name .= " ({$this->company})";
        }

        return $name;
    }

    /**
     * Get contact initials for avatar.
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            if (! empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        return substr($initials, 0, 2);
    }
}
