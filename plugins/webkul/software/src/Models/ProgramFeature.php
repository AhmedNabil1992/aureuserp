<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Product\Models\Product;
use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Services\CatalogProductSyncService;

class ProgramFeature extends Model
{
    use HasFactory;

    protected $table = 'software_program_features';

    protected $fillable = [
        'program_id',
        'name',
        'service_type',
        'product_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'service_type' => ServiceType::class,
    ];

    protected static function booted(): void
    {
        static::saved(function (self $feature): void {
            if (! $feature->wasRecentlyCreated
                && ! $feature->wasChanged(['name', 'program_id', 'amount', 'product_id'])
                && filled($feature->product_id)) {
                return;
            }

            app(CatalogProductSyncService::class)->syncFeature($feature);
        });
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function editionRules(): HasMany
    {
        return $this->hasMany(ProgramEditionFeature::class, 'program_feature_id');
    }
}
