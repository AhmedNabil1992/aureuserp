<?php

namespace Webkul\TechnicalSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Webkul\TechnicalSupport\Enums\ServiceType;

class QuickDownload extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'technical_support_quick_downloads';

    protected $fillable = [
        'title',
        'description',
        'service_type',
        'file_path',
        'external_url',
        'version',
        'file_size',
        'downloads_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'service_type'    => ServiceType::class,
        'downloads_count' => 'integer',
        'is_active'       => 'boolean',
        'sort_order'      => 'integer',
    ];

    public function getDownloadUrlAttribute(): ?string
    {
        if (! empty($this->external_url)) {
            return $this->external_url;
        }

        if (! empty($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }

        return null;
    }
}
