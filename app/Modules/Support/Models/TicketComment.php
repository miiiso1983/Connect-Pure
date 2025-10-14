<?php

namespace App\Modules\Support\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketComment extends Model
{
    use HasFactory;

    protected $table = 'ticket_comments';

    protected $fillable = [
        'ticket_id',
        'comment',
        'author_name',
        'author_email',
        'author_type',
        'is_internal',
        'is_solution',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_solution' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'comment_id');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeSolutions($query)
    {
        return $query->where('is_solution', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    // Accessors & Mutators
    public function getIsEditedAttribute()
    {
        return ! is_null($this->edited_at);
    }

    public function getFormattedCommentAttribute()
    {
        // Convert line breaks to HTML
        return nl2br(e($this->comment));
    }

    public function getAuthorNameAttribute()
    {
        return $this->createdBy ? $this->createdBy->name : 'Unknown User';
    }

    public function getAuthorAvatarAttribute()
    {
        if ($this->createdBy && $this->createdBy->avatar) {
            return $this->createdBy->avatar;
        }

        // Generate avatar URL based on email or name
        $email = $this->createdBy ? $this->createdBy->email : 'unknown@example.com';

        return 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?d=identicon&s=40';
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Methods
    public function markAsSolution(): void
    {
        // Remove solution flag from other comments
        $this->ticket->comments()->where('id', '!=', $this->id)->update(['is_solution' => false]);

        // Mark this comment as solution
        $this->update(['is_solution' => true]);
    }

    public function unmarkAsSolution(): void
    {
        $this->update(['is_solution' => false]);
    }

    public function edit(string $newComment, int $userId): void
    {
        $this->update([
            'comment' => $newComment,
            'edited_at' => now(),
            'edited_by' => $userId,
        ]);
    }

    public function addAttachment(array $attachmentData): TicketAttachment
    {
        return $this->attachments()->create($attachmentData);
    }

    public function toggleInternal(): void
    {
        $this->update(['is_internal' => ! $this->is_internal]);
    }

    // Static methods
    public static function createSystemComment(int $ticketId, string $message): self
    {
        return self::create([
            'ticket_id' => $ticketId,
            'comment' => $message,
            'created_by' => null, // System comment
            'is_internal' => true,
        ]);
    }

    public static function getCommentStatistics(int $ticketId): array
    {
        $comments = self::where('ticket_id', $ticketId);

        return [
            'total' => $comments->count(),
            'public' => $comments->where('is_internal', false)->count(),
            'internal' => $comments->where('is_internal', true)->count(),
            'solutions' => $comments->where('is_solution', true)->count(),
            'with_attachments' => $comments->whereHas('attachments')->count(),
        ];
    }
}
