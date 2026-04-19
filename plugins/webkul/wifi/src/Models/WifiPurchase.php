<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Models\MoveLine;
use Webkul\Security\Models\User;

class WifiPurchase extends Model
{
    protected $table = 'wifi_purchases';

    protected $fillable = [
        'wifi_package_id',
        'move_line_id',
        'cloud_id',
        'quantity',
        'remaining_quantity',
        'is_default',
        'creator_id',
    ];

    public function casts(): array
    {
        return [
            'cloud_id'           => 'integer',
            'quantity'           => 'integer',
            'remaining_quantity' => 'integer',
            'is_default'         => 'boolean',
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
        if ($this->relationLoaded('voucherBatches')) {
            return (int) $this->voucherBatches->sum('quantity');
        }

        return (int) $this->voucherBatches()->sum('quantity');
    }

    public function getDisplayNameAttribute(): string
    {
        $invoiceName = $this->invoiceLine?->move?->name ?? 'Invoice line';
        $packageName = $this->package?->display_name ?? 'Package';

        return sprintf('%s - %s', $invoiceName, $packageName);
    }

    public function refreshRemainingQuantity(): void
    {
        $generatedQuantity = (int) $this->voucherBatches()->sum('quantity');
        $remainingQuantity = max(0, (int) $this->quantity - $generatedQuantity);

        if ((int) $this->remaining_quantity !== $remainingQuantity) {
            $this->remaining_quantity = $remainingQuantity;
            $this->saveQuietly();
        }
    }

    protected static function booted(): void
    {
        static::creating(function (self $purchase): void {
            $purchase->creator_id ??= Auth::id();
        });

        static::saving(function (self $purchase): void {
            $purchase->loadMissing(['package.product', 'invoiceLine']);

            if (! $purchase->package || ! $purchase->invoiceLine) {
                throw ValidationException::withMessages([
                    'wifi_package_id' => 'Package and invoice line are required.',
                ]);
            }

            if ((int) $purchase->invoiceLine->product_id !== (int) $purchase->package->product_id) {
                throw ValidationException::withMessages([
                    'move_line_id' => 'The selected invoice line must belong to the same service product as the Wi-Fi package.',
                ]);
            }

            if (blank($purchase->quantity)) {
                $purchase->quantity = max(1, (int) round(((float) $purchase->invoiceLine->quantity) * $purchase->package->quantity));
            }

            $generatedQuantity = $purchase->exists ? (int) $purchase->voucherBatches()->sum('quantity') : 0;

            if ((int) $purchase->quantity < $generatedQuantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Purchase quantity cannot be less than the already generated vouchers.',
                ]);
            }

            $purchase->remaining_quantity = max(0, (int) $purchase->quantity - $generatedQuantity);
        });
    }
}
