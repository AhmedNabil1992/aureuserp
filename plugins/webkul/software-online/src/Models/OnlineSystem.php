<?php

namespace Webkul\SoftwareOnline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineSystem extends Model
{
    use SoftDeletes;

    protected $table = 'online_systems';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'logo',
        'base_url',
        'is_active',
        'sort_order',
        'api_driver',
        'api_base_url',
        'api_token',
        'api_secret',
        'api_headers',
        'create_tenant_endpoint',
        'renew_tenant_endpoint',
        'suspend_tenant_endpoint',
        'activate_tenant_endpoint',
        'delete_tenant_endpoint',
        'sync_status_endpoint',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'sort_order'   => 'integer',
        'api_headers'  => 'array',
        'api_token'    => 'encrypted',
        'api_secret'   => 'encrypted',
    ];

    public function plans(): HasMany
    {
        return $this->hasMany(OnlineSystemPlan::class, 'system_id')->orderBy('sort_order');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(OnlineInstance::class, 'system_id');
    }
}
