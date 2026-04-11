<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemoteProfile extends Model
{
    use HasFactory;

    protected $table = 'software_remote_profiles';

    protected $fillable = [
        'license_id',
        'anydesk',
        'teamviewer',
        'rustdesk',
        'remark',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }
}
