<?php

namespace Webkul\Marketing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Lead\Models\Lead;
use Webkul\Marketing\Enums\AdPlatform;
use Webkul\Marketing\Enums\CampaignStatus;
use Webkul\Security\Models\User;

class Campaign extends Model
{
    use HasChatter, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'marketing_campaigns';

    protected $fillable = [
        'name',
        'platform',
        'status',
        'month',
        'description',
        'assigned_to',
        'creator_id',
    ];

    public function casts(): array
    {
        return [
            'platform' => AdPlatform::class,
            'status'   => CampaignStatus::class,
            'month'    => 'date',
        ];
    }

    public function getModelTitle(): string
    {
        return $this->name;
    }

    public function getLogAttributeLabels(): array
    {
        return [
            'name'        => 'Name',
            'platform'    => 'Platform',
            'status'      => 'Status',
            'month'       => 'Month',
            'description' => 'Description',
            'assigned_to' => 'Assigned To',
        ];
    }

    public function adPlan(): HasOne
    {
        return $this->hasOne(AdPlan::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'campaign_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($campaign) {
            $campaign->creator_id ??= Auth::id();
        });
    }
}
