<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class TicketAttachment extends Model
{
    protected $table = 'software_ticket_attachments';

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
        return Storage::url($this->file_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }
}
