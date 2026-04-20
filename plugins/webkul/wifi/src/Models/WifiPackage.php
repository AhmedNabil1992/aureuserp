<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;
use Webkul\Wifi\Enums\WifiPackageType;

class WifiPackage extends Model
{
    protected $table = 'wifi_packages';

    protected $fillable = [
        'product_id',
        'currency_id',
        'description',
        'package_type',
        'quantity',
        'amount',
        'dealer_amount',
        'is_active',
        'creator_id',
    ];

    public function casts(): array
    {
        return [
            'package_type'   => WifiPackageType::class,
            'currency_id'    => 'integer',
            'quantity'       => 'integer',
            'amount'         => 'decimal:4',
            'dealer_amount'  => 'decimal:4',
            'is_active'      => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(WifiPurchase::class, 'wifi_package_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $productName = $this->product?->name ?? 'Unknown Service';

        return sprintf('%s (%s - %d cards)', $productName, $this->package_type?->getLabel() ?? 'Package', $this->quantity ?? 0);
    }

    protected static function booted(): void
    {
        static::creating(function (self $package): void {
            $package->creator_id ??= Auth::id();
        });

        static::saving(function (self $package): void {
            if ($package->quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Package quantity must be at least 1.',
                ]);
            }

            if (($package->amount ?? 0) < 0 || (($package->dealer_amount ?? 0) < 0)) {
                throw ValidationException::withMessages([
                    'amount' => 'Amounts must be greater than or equal to zero.',
                ]);
            }

            if (! $package->currency_id) {
                throw ValidationException::withMessages([
                    'currency_id' => 'Please select a currency for this package.',
                ]);
            }

            if (! $package->relationLoaded('product')) {
                $package->loadMissing('product');
            }

            $productType = $package->product?->type;
            $productValue = $productType instanceof ProductType ? $productType->value : $productType;

            if ($productValue !== ProductType::SERVICE->value) {
                throw ValidationException::withMessages([
                    'product_id' => 'Wi-Fi packages must be linked to a service product.',
                ]);
            }
        });
    }
}
