<?php

declare(strict_types=1);

namespace Webkul\Referral\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
use Webkul\Referral\Enums\RedemptionStatus;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class ReferralRedemption extends Model
{
    protected $table = 'referral_redemptions';

    protected $fillable = [
        'campaign_id',
        'referral_code_id',
        'company_id',
        'currency_id',
        'referrer_partner_id',
        'referred_partner_id',
        'move_id',
        'reward_move_id',
        'reward_reversal_move_id',
        'first_purchase_key',
        'source_type',
        'source_id',
        'context',
        'status',
        'eligible_amount',
        'customer_discount',
        'referrer_reward',
        'snapshot',
        'earned_at',
        'reversed_at',
        'creator_id',
    ];

    protected function casts(): array
    {
        return [
            'status'            => RedemptionStatus::class,
            'eligible_amount'   => 'decimal:4',
            'customer_discount' => 'decimal:4',
            'referrer_reward'   => 'decimal:4',
            'snapshot'          => 'array',
            'earned_at'         => 'datetime',
            'reversed_at'       => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(ReferralCampaign::class, 'campaign_id');
    }

    public function referralCode(): BelongsTo
    {
        return $this->belongsTo(ReferralCode::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'referrer_partner_id');
    }

    public function referredPartner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'referred_partner_id');
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'move_id');
    }

    public function rewardMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'reward_move_id');
    }

    public function rewardReversalMove(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'reward_reversal_move_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $redemption): void {
            $redemption->creator_id ??= Auth::guard('web')->id();
        });
    }
}
