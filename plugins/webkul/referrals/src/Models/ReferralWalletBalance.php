<?php

declare(strict_types=1);

namespace Webkul\Referral\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralWalletBalance extends Model
{
    protected $table = 'referral_wallet_balances';

    protected $fillable = ['company_id', 'currency_id', 'partner_id', 'available_amount', 'lifetime_earned', 'lifetime_redeemed'];

    protected function casts(): array
    {
        return ['available_amount' => 'decimal:4', 'lifetime_earned' => 'decimal:4', 'lifetime_redeemed' => 'decimal:4'];
    }
}
