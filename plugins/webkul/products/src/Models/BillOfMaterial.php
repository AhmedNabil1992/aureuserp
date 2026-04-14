<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Models\Location;
use Webkul\Product\Enums\BomType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class BillOfMaterial extends Model
{
    protected $table = 'products_bill_of_materials';

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'uom_id',
        'reference',
        'notes',
        'company_id',
        'source_location_id',
        'creator_id',
    ];

    protected $casts = [
        'type'     => BomType::class,
        'quantity' => 'decimal:4',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sourceLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'source_location_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BillOfMaterialLine::class)->orderBy('sort')->orderBy('id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $billOfMaterial): void {
            $billOfMaterial->creator_id ??= Auth::id();
        });
    }
}
