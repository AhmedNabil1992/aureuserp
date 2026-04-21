<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\User;

class WifiVoucherBatch extends Model
{
    protected $table = 'wifi_voucher_batches';

    protected $fillable = [
        'wifi_purchase_id',
        'cloud_id',
        'realm_id',
        'nasidentifier',
        'profile_id',
        'days_valid',
        'hours_valid',
        'minutes_valid',
        'batch_code',
        'quantity',
        'never_expire',
        'caption',
        'creator_id',
    ];

    public function casts(): array
    {
        return [
            'cloud_id'          => 'integer',
            'realm_id'          => 'integer',
            'profile_id'        => 'integer',
            'days_valid'        => 'integer',
            'hours_valid'       => 'integer',
            'minutes_valid'     => 'integer',
            'quantity'          => 'integer',
            'never_expire'      => 'boolean',
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(WifiPurchase::class, 'wifi_purchase_id');
    }

    public function cloud(): BelongsTo
    {
        return $this->belongsTo(Cloud::class, 'cloud_id', 'id');
    }

    public function realm(): BelongsTo
    {
        return $this->belongsTo(Realm::class, 'realm_id', 'id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }

    public function accessPoint(): BelongsTo
    {
        return $this->belongsTo(DynamicClient::class, 'nasidentifier', 'nasidentifier');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return sprintf('%s (%d)', $this->batch_code, $this->quantity ?? 0);
    }

    protected static function booted(): void
    {
        static::creating(function (self $batch): void {
            $batch->creator_id ??= Auth::id();
            $batch->batch_code ??= 'WIFI-'.Str::upper(Str::random(10));
        });

        static::saving(function (self $batch): void {
            $batch->loadMissing(['purchase']);

            if (! $batch->purchase) {
                throw ValidationException::withMessages([
                    'wifi_purchase_id' => 'A Wi-Fi purchase is required before creating a voucher batch.',
                ]);
            }

            if ($batch->quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Batch quantity must be at least 1.',
                ]);
            }

            if ($batch->purchase->cloud_id && $batch->cloud_id && (int) $batch->purchase->cloud_id !== (int) $batch->cloud_id) {
                throw ValidationException::withMessages([
                    'cloud_id' => 'Batch cloud must match the cloud assigned to the purchase.',
                ]);
            }

            $batch->cloud_id ??= $batch->purchase->cloud_id;

            if (filled($batch->nasidentifier) && ! DynamicClient::query()->where('nasidentifier', $batch->nasidentifier)->exists()) {
                throw ValidationException::withMessages([
                    'nasidentifier' => 'Selected access point does not exist.',
                ]);
            }

            $availableQuantity = (int) $batch->purchase->remaining_quantity;

            if ($batch->exists) {
                $availableQuantity += (int) $batch->getOriginal('quantity');
            }

            if ((int) $batch->quantity > $availableQuantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Batch quantity cannot exceed the remaining available quantity for this purchase.',
                ]);
            }
        });

        static::saved(function (self $batch): void {
            $batch->purchase?->refreshRemainingQuantity();
        });

        static::deleted(function (self $batch): void {
            $batch->purchase?->refreshRemainingQuantity();
        });
    }
}
