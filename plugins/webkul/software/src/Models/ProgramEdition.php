<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Product\Models\Product;
use Webkul\Software\Services\CatalogProductSyncService;

class ProgramEdition extends Model
{
    use HasFactory;

    protected $table = 'software_program_editions';

    protected $fillable = [
        'program_id',
        'variant_product_id',
        'name',
        'max_devices',
        'license_cost',
        'license_price',
        'monthly_renewal',
        'annual_renewal',
    ];

    protected $casts = [
        'max_devices'     => 'integer',
        'license_cost'    => 'decimal:2',
        'license_price'   => 'decimal:2',
        'monthly_renewal' => 'decimal:2',
        'annual_renewal'  => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $edition): void {
            if (! $edition->wasRecentlyCreated
                && ! $edition->wasChanged(['name', 'program_id', 'license_price', 'variant_product_id'])
                && filled($edition->variant_product_id)) {
                return;
            }

            app(CatalogProductSyncService::class)->syncEdition($edition);
        });
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function variantProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'variant_product_id');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'edition_id');
    }

    public function featureRules(): HasMany
    {
        return $this->hasMany(ProgramEditionFeature::class, 'program_edition_id')->orderBy('sort_order');
    }
}
