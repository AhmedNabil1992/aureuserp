<?php

namespace Webkul\Marketing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class AdPlan extends Model
{
    use HasFactory;

    protected $table = 'marketing_ad_plans';

    protected $fillable = [
        'campaign_id',
        'planned_budget',
        'planned_reach',
        'planned_messages',
        'planned_conversions',
        'actual_budget',
        'actual_reach',
        'actual_messages',
        'actual_conversions',
        'actual_leads',
        'notes',
        'creator_id',
    ];

    public function casts(): array
    {
        return [
            'planned_budget'  => 'decimal:2',
            'actual_budget'   => 'decimal:2',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getBudgetVarianceAttribute(): float
    {
        if ($this->actual_budget === null) {
            return 0;
        }

        return (float) $this->actual_budget - (float) $this->planned_budget;
    }

    public function getConversionRateAttribute(): float
    {
        $messages = $this->actual_messages ?? $this->planned_messages;

        if (! $messages) {
            return 0;
        }

        $conversions = $this->actual_conversions ?? 0;

        return round(($conversions / $messages) * 100, 2);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($plan) {
            $plan->creator_id ??= Auth::id();
        });
    }
}
