<?php

namespace Webkul\TechnicalSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class TicketAttachment extends Model
{
    protected $table = 'technical_support_ticket_attachments';

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    protected $appends = ['url'];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function isAudio(): bool
    {
        if (str_starts_with($this->mime_type ?? '', 'audio/')) {
            return true;
        }

        $ext = strtolower(pathinfo($this->original_name ?? $this->file_path, PATHINFO_EXTENSION));

        return in_array($ext, ['webm', 'mp3', 'wav', 'ogg', 'm4a', 'aac']);
    }
}
