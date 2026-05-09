<?php

namespace Webkul\Account\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Account\Database\Factories\BalanceRequestFactory;
use Webkul\Partner\Models\Partner;
use Webkul\Payment\Models\PaymentTransaction;

class BalanceRequest extends Model
{
    use HasFactory;

    protected $table = 'accounts_balance_requests';

    protected $fillable = [
        'partner_id',
        'payment_transaction_id',
        'amount',
        'status',
        'requested_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
    ];

    /**
     * Status options for balance requests.
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING  => 'قيد الانتظار',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_REJECTED => 'مرفوض',
        ];
    }

    /**
     * Get the customer (partner) who requested the balance.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class, 'payment_transaction_id');
    }

    /**
     * Get the admin who approved this request.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if this request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if there's a pending request for this customer.
     */
    public static function hasPendingRequest(int $partnerId): bool
    {
        return static::where('partner_id', $partnerId)
            ->where('status', self::STATUS_PENDING)
            ->exists();
    }

    /**
     * Get the last pending request for a customer (if any).
     */
    public static function getPendingRequest(int $partnerId): ?self
    {
        return static::where('partner_id', $partnerId)
            ->where('status', self::STATUS_PENDING)
            ->latest()
            ->first();
    }

    protected static function newFactory(): Factory
    {
        return BalanceRequestFactory::new();
    }
}
