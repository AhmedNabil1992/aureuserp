<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Models\MoveLine;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;

class WifiPurchase extends Model
{
    protected $table = 'wifi_purchases';

    protected $fillable = [
        'wifi_package_id',
        'partner_id',
        'move_line_id',
        'legacy_purchase_id',
        'cloud_id',
        'quantity',
        'remaining_quantity',
        'legacy_consumed_quantity',
        'is_default',
        'creator_id',
    ];

    public function casts(): array
    {
        return [
            'cloud_id'                 => 'integer',
            'partner_id'               => 'integer',
            'legacy_purchase_id'       => 'integer',
            'quantity'                 => 'integer',
            'remaining_quantity'       => 'integer',
            'legacy_consumed_quantity' => 'integer',
            'is_default'               => 'boolean',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(WifiPackage::class, 'wifi_package_id');
    }

    public function invoiceLine(): BelongsTo
    {
        return $this->belongsTo(MoveLine::class, 'move_line_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function cloud(): BelongsTo
    {
        return $this->belongsTo(Cloud::class, 'cloud_id', 'id');
    }

    public function voucherBatches(): HasMany
    {
        return $this->hasMany(WifiVoucherBatch::class, 'wifi_purchase_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getGeneratedQuantityAttribute(): int
    {
        $legacyConsumedQuantity = (int) $this->legacy_consumed_quantity;

        if ($this->relationLoaded('voucherBatches')) {
            return $legacyConsumedQuantity + (int) $this->voucherBatches->sum('quantity');
        }

        return $legacyConsumedQuantity + (int) $this->voucherBatches()->sum('quantity');
    }

    public function getDisplayNameAttribute(): string
    {
        $invoiceName = $this->invoiceLine?->move?->name
            ?? ($this->legacy_purchase_id ? "Legacy #{$this->legacy_purchase_id}" : 'Purchase');
        $packageName = $this->package?->display_name ?? 'Package';

        return sprintf('%s - %s', $invoiceName, $packageName);
    }

    public function refreshRemainingQuantity(): void
    {
        $generatedQuantity = (int) $this->legacy_consumed_quantity
            + (int) $this->voucherBatches()->sum('quantity');
        $remainingQuantity = max(0, (int) $this->quantity - $generatedQuantity);

        if ((int) $this->remaining_quantity !== $remainingQuantity) {
            $this->remaining_quantity = $remainingQuantity;
            $this->saveQuietly();
        }
    }

    public function scopeForPartner(Builder $query, int $partnerId): Builder
    {
        return $query->where('partner_id', $partnerId);
    }

    protected static function booted(): void
    {
        static::creating(function (self $purchase): void {
            if (! $purchase->creator_id && Auth::user() instanceof User) {
                $purchase->creator_id = Auth::id();
            }
        });

        static::saving(function (self $purchase): void {
            $purchase->loadMissing(['package.product', 'invoiceLine.move']);

            if (! $purchase->package) {
                throw ValidationException::withMessages([
                    'wifi_package_id' => 'A Wi-Fi package is required.',
                ]);
            }

            if (! $purchase->invoiceLine && ! $purchase->legacy_purchase_id) {
                throw ValidationException::withMessages([
                    'move_line_id' => 'An invoice line or legacy purchase reference is required.',
                ]);
            }

            if ($purchase->invoiceLine && (int) $purchase->invoiceLine->product_id !== (int) $purchase->package->product_id) {
                throw ValidationException::withMessages([
                    'move_line_id' => 'The selected invoice line must belong to the same service product as the Wi-Fi package.',
                ]);
            }

            if (! $purchase->partner_id && $purchase->invoiceLine?->move?->partner_id) {
                $purchase->partner_id = $purchase->invoiceLine->move->partner_id;
            }

            if (! $purchase->partner_id) {
                throw ValidationException::withMessages([
                    'partner_id' => 'A customer is required.',
                ]);
            }

            if (blank($purchase->quantity) && $purchase->invoiceLine) {
                $purchase->quantity = max(1, (int) round(((float) $purchase->invoiceLine->quantity) * $purchase->package->quantity));
            }

            $generatedQuantity = (int) $purchase->legacy_consumed_quantity
                + ($purchase->exists ? (int) $purchase->voucherBatches()->sum('quantity') : 0);

            if ((int) $purchase->quantity < $generatedQuantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Purchase quantity cannot be less than the already generated vouchers.',
                ]);
            }

            $purchase->remaining_quantity = max(0, (int) $purchase->quantity - $generatedQuantity);
        });
    }
}
