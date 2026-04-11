<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\Software\Enums\ErrorLogStatus;

class ErrorLog extends Model
{
    use HasFactory;

    protected $table = 'software_error_logs';

    protected $fillable = [
        'device_id',
        'eid',
        'message',
        'trace',
        'form_name',
        'image_path',
        'app_version',
        'status',
        'checked_by',
        'occurred_at',
    ];

    protected $casts = [
        'eid'         => 'integer',
        'status'      => ErrorLogStatus::class,
        'occurred_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(LicenseDevice::class, 'device_id');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
