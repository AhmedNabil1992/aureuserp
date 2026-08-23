<?php

namespace Webkul\TechnicalSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\TechnicalSupport\Enums\ServiceType;

class ServiceStaffAssignment extends Model
{
    protected $table = 'technical_support_service_staff';

    protected $fillable = [
        'service_type',
        'service_reference_id',
        'user_id',
    ];

    protected $casts = [
        'service_type' => ServiceType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
