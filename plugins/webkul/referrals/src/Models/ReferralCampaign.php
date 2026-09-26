<?php

declare(strict_types=1);

namespace Webkul\Referral\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Product\Models\Product;
use Webkul\Referral\Enums\DiscountType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class ReferralCampaign extends Model
{
    protected $table = 'referral_campaigns';

    protected $fillable = [
        'company_id',
        'currency_id',
        'journal_id',
        'expense_account_id',
        'name',
        'contexts',
        'discount_type',
        'customer_discount',
        'referrer_reward',
        'minimum_eligible_amount',
        'first_purchase_only',
        'is_active',
        'max_redemptions',
        'starts_at',
        'ends_at',
        'creator_id',
    ];

    protected function casts(): array
    {
        return [
            'contexts'                => 'array',
            'discount_type'           => DiscountType::class,
            'customer_discount'       => 'decimal:4',
            'referrer_reward'         => 'decimal:4',
            'minimum_eligible_amount' => 'decimal:4',
            'first_purchase_only'     => 'boolean',
            'is_active'               => 'boolean',
            'starts_at'               => 'datetime',
            'ends_at'                 => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'referral_campaign_products', 'campaign_id', 'product_id')
            ->withTimestamps();
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(ReferralRedemption::class, 'campaign_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $campaign): void {
            $campaign->creator_id ??= Auth::guard('web')->id();
        });
    }
}
