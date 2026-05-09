<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Partner\Models\Partner;

class CustomerCredit extends Model
{
    protected $table = 'accounts_customer_credits';

    protected $fillable = [
        'partner_id',
        'balance',
        'reserved_amount',
        'status',
    ];

    protected $casts = [
        'balance'         => 'decimal:2',
        'reserved_amount' => 'decimal:2',
    ];

    /**
     * Get the partner (customer) associated with this credit account.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the available credit balance (total balance - reserved for pending requests).
     */
    public function getAvailableBalanceAttribute(): float
    {
        return max(0, (float) $this->balance - (float) $this->reserved_amount);
    }
}
