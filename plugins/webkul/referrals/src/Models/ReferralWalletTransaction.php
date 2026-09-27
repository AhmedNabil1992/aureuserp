<?php

declare(strict_types=1);

namespace Webkul\Referral\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Currency;

class ReferralWalletTransaction extends Model
{
    protected $table = 'referral_wallet_transactions';

    protected $fillable = [
        'company_id', 'currency_id', 'partner_id', 'referral_redemption_id', 'accounting_move_id',
        'invoice_move_id', 'type', 'amount', 'idempotency_key', 'metadata', 'creator_id',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:4', 'metadata' => 'array'];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function accountingMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'accounting_move_id');
    }

    public function invoiceMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'invoice_move_id');
    }
}
