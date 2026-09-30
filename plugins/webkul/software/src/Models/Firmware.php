<?php

declare(strict_types=1);

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Firmware extends Model
{
    protected $table = 'software_firmwares';

    protected $fillable = [
        'device_model',
        'version',
        'file_path',
        'original_file_name',
        'file_size',
        'md5',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Firmware $firmware): void {
            if (! $firmware->isDirty('file_path') || blank($firmware->file_path)) {
                return;
            }

            $disk = Storage::disk('local');
            $firmware->original_file_name = $firmware->original_file_name ?: basename($firmware->file_path);
            $firmware->file_size = $disk->size($firmware->file_path);
            $firmware->md5 = md5_file($disk->path($firmware->file_path));
        });

        static::updated(function (Firmware $firmware): void {
            $oldPath = $firmware->getOriginal('file_path');

            if ($firmware->wasChanged('file_path') && filled($oldPath)) {
                Storage::disk('local')->delete($oldPath);
            }
        });

        static::deleted(function (Firmware $firmware): void {
            Storage::disk('local')->delete($firmware->file_path);
        });
    }
}
