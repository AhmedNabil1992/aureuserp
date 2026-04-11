<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LicenseDevice extends Model
{
    use HasFactory;

    protected $table = 'software_license_devices';

    protected $fillable = [
        'license_id',
        'computer_id',
        'license_key',
        'bios_id',
        'disk_id',
        'base_id',
        'video_id',
        'mac_id',
        'device_name',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    public function errorLogs(): HasMany
    {
        return $this->hasMany(ErrorLog::class, 'device_id');
    }
}
