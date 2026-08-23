<?php

namespace Webkul\SoftwareOnline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Partner\Models\Partner;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;

class OnlineInstance extends Model
{
    use SoftDeletes;

    protected $table = 'online_instances';

    protected $fillable = [
        'instance_number',
        'partner_id',
        'system_id',
        'plan_id',
        'name',
        'subdomain',
        'custom_domain',
        'instance_url',
        'admin_email',
        'admin_username',
        'billing_cycle',
        'price',
        'move_id',
        'status',
        'starts_at',
        'expires_at',
        'last_renewed_at',
        'auto_renew',
        'remote_tenant_id',
        'remote_data',
        'last_api_sync_at',
        'last_api_error',
    ];

    protected $casts = [
        'instance_number'  => 'integer',
        'price'            => 'decimal:2',
        'status'           => InstanceStatus::class,
        'billing_cycle'    => BillingCycle::class,
        'starts_at'        => 'datetime',
        'expires_at'       => 'datetime',
        'last_renewed_at'  => 'datetime',
        'auto_renew'       => 'boolean',
        'remote_data'      => 'array',
        'last_api_sync_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->instance_number)) {
                $max = static::max('instance_number') ?? 1000;
                $model->instance_number = $max + 1;
            }
        });
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(OnlineSystem::class, 'system_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(OnlineSystemPlan::class, 'plan_id');
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Account\Models\Move::class, 'move_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(OnlineInstanceTransaction::class, 'instance_id');
    }

    public function getFullUrlAttribute(): string
    {
        if (! empty($this->instance_url)) {
            return $this->instance_url;
        }

        if (! empty($this->custom_domain)) {
            return 'https://' . ltrim($this->custom_domain, 'https://');
        }

        if (! empty($this->subdomain) && ! empty($this->system?->base_url)) {
            $base = $this->system->base_url;
            if (str_contains($base, '{subdomain}')) {
                return str_replace('{subdomain}', $this->subdomain, $base);
            }

            $parsed = parse_url($base);
            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? $base;

            return "{$scheme}://{$this->subdomain}.{$host}";
        }

        return $this->system?->base_url ?? '#';
    }

    public function isActive(): bool
    {
        return $this->status === InstanceStatus::Active
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
