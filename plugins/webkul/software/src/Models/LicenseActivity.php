<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseActivity extends Model
{
    use HasFactory;

    protected $table = 'software_license_activities';

    protected $fillable = [
        'license_id',
        'current_version',
        'last_online_at',
    ];

    protected $casts = [
        'last_online_at' => 'datetime',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }
}
