<?php

namespace App\Modules\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $table = 'ticket_attachments';

    protected $fillable = [
        'ticket_id',
        'ticket_comment_id',
        'original_name',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Relationships
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(TicketComment::class, 'ticket_comment_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeDocuments($query)
    {
        return $query->whereNotLike('mime_type', 'image/%');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    // Accessors & Mutators
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getFileExtensionAttribute()
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    public function getIsImageAttribute()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getIsDocumentAttribute()
    {
        return in_array($this->file_extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
    }

    public function getIsVideoAttribute()
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function getIsAudioAttribute()
    {
        return str_starts_with($this->mime_type, 'audio/');
    }

    public function getDownloadUrlAttribute()
    {
        return route('support.attachments.download', $this->id);
    }

    public function getPreviewUrlAttribute()
    {
        if ($this->is_image) {
            return Storage::url($this->file_path);
        }
        
        return null;
    }

    public function getFileIconAttribute()
    {
        if ($this->is_image) {
            return 'fas fa-image';
        } elseif ($this->is_document) {
            return match($this->file_extension) {
                'pdf' => 'fas fa-file-pdf',
                'doc', 'docx' => 'fas fa-file-word',
                'xls', 'xlsx' => 'fas fa-file-excel',
                'ppt', 'pptx' => 'fas fa-file-powerpoint',
                default => 'fas fa-file-alt',
            };
        } elseif ($this->is_video) {
            return 'fas fa-file-video';
        } elseif ($this->is_audio) {
            return 'fas fa-file-audio';
        } else {
            return 'fas fa-file';
        }
    }

    public function getUploaderNameAttribute()
    {
        return $this->uploadedBy ? $this->uploadedBy->name : 'Unknown User';
    }

    public function getUploadedTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Methods
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    public function toggleVisibility(): void
    {
        $this->update(['is_public' => !$this->is_public]);
    }

    public function delete(): bool
    {
        // Delete the physical file
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        // Delete the database record
        return parent::delete();
    }

    public function getFileContent(): string
    {
        return Storage::get($this->file_path);
    }

    public function exists(): bool
    {
        return Storage::exists($this->file_path);
    }

    // Static methods
    public static function getAllowedMimeTypes(): array
    {
        return [
            // Images
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            
            // Text files
            'text/plain',
            'text/csv',
            'application/json',
            'application/xml',
            
            // Archives
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
        ];
    }

    public static function getMaxFileSize(): int
    {
        return 10 * 1024 * 1024; // 10MB
    }

    public static function getAttachmentStatistics(int $ticketId): array
    {
        $attachments = self::where('ticket_id', $ticketId);
        
        return [
            'total' => $attachments->count(),
            'images' => $attachments->images()->count(),
            'documents' => $attachments->documents()->count(),
            'total_size' => $attachments->sum('file_size'),
            'total_downloads' => $attachments->sum('download_count'),
            'public' => $attachments->where('is_public', true)->count(),
            'private' => $attachments->where('is_public', false)->count(),
        ];
    }

    public static function cleanupOrphanedFiles(): int
    {
        $deletedCount = 0;
        $attachments = self::all();
        
        foreach ($attachments as $attachment) {
            if (!$attachment->exists()) {
                $attachment->delete();
                $deletedCount++;
            }
        }
        
        return $deletedCount;
    }
}
