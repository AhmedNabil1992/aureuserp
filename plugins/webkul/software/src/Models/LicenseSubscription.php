<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseSubscription extends Model
{
    use HasFactory;

    protected $table = 'software_license_subscriptions';

    protected $fillable = [
        'license_id',
        'feature_id',
        'service_type',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(ProgramFeature::class, 'feature_id');
    }

    public function scopeOfType(Builder $query, string $serviceType): Builder
    {
        return $query->where('service_type', $serviceType);
    }

    public function scopeActiveNow(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', now()->toDateString());
            })
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            });
    }
}
