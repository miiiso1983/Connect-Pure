<?php

namespace App\Models\Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'company',
        'phone',
        'email',
        'type',
        'status',
        'notes',
        'next_follow_up',
        'potential_value',
        'source',
        'assigned_to'
    ];

    protected $casts = [
        'next_follow_up' => 'datetime',
        'potential_value' => 'decimal:2'
    ];

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class)->orderBy('communication_date', 'desc');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class)->orderBy('scheduled_date', 'asc');
    }

    public function pendingFollowUps(): HasMany
    {
        return $this->hasMany(FollowUp::class)
            ->where('status', 'pending')
            ->orderBy('scheduled_date', 'asc');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new' => 'gray',
            'contacted' => 'blue',
            'qualified' => 'yellow',
            'proposal' => 'purple',
            'negotiation' => 'orange',
            'closed_won' => 'green',
            'closed_lost' => 'red',
            default => 'gray'
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'lead' => 'blue',
            'client' => 'green',
            default => 'gray'
        };
    }
}
