<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineTenantWebhookEvent extends Model
{
    protected $table = 'online_tenant_webhook_events';

    protected $fillable = [
        'system_id',
        'instance_id',
        'event_id',
        'event_type',
        'status',
        'occurred_at',
        'payload',
        'error',
        'processed_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'payload'     => 'array',
        'processed_at'=> 'datetime',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(OnlineSystem::class, 'system_id');
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(OnlineInstance::class, 'instance_id');
    }
}
