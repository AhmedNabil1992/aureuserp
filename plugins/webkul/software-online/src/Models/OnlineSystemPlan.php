<?php

namespace Webkul\SoftwareOnline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineSystemPlan extends Model
{
    use SoftDeletes;

    protected $table = 'online_system_plans';

    protected $fillable = [
        'system_id',
        'product_id',
        'name',
        'slug',
        'description',
        'features',
        'monthly_price',
        'annual_price',
        'currency_code',
        'trial_days',
        'max_users',
        'max_branches',
        'custom_api_payload',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features'           => 'array',
        'custom_api_payload' => 'array',
        'monthly_price'      => 'decimal:2',
        'annual_price'       => 'decimal:2',
        'trial_days'         => 'integer',
        'max_users'          => 'integer',
        'max_branches'       => 'integer',
        'is_active'          => 'boolean',
        'sort_order'         => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Product\Models\Product::class, 'product_id');
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(OnlineSystem::class, 'system_id');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(OnlineInstance::class, 'plan_id');
    }
}
