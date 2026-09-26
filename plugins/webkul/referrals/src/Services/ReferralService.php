<?php

declare(strict_types=1);

namespace Webkul\Referral\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Partner\Models\Partner;
use Webkul\Referral\Enums\DiscountType;
use Webkul\Referral\Enums\RedemptionStatus;
use Webkul\Referral\Models\ReferralCampaign;
use Webkul\Referral\Models\ReferralCode;
use Webkul\Referral\Models\ReferralRedemption;

class ReferralService
{
    public const CONTEXT_LICENSE = 'license';

    public const CONTEXT_ONLINE = 'online_subscription';

    public const CONTEXT_SALES_INVOICE = 'sales_invoice';

    public function codeForPartner(Partner $partner, ?int $companyId = null): ReferralCode
    {
        $companyId ??= (int) ($partner->company_id ?: current_company_id());

        if (! $companyId) {
            $existingCode = ReferralCode::query()
                ->where('partner_id', $partner->id)
                ->orderByDesc('is_active')
                ->orderBy('id')
                ->first();

            if ($existingCode) {
                return $existingCode;
            }

            $activeCampaignCompanyIds = ReferralCampaign::query()
                ->active()
                ->distinct()
                ->limit(2)
                ->pluck('company_id');

            if ($activeCampaignCompanyIds->count() === 1) {
                $companyId = (int) $activeCampaignCompanyIds->first();
            }
        }

        if (! $companyId) {
            throw ValidationException::withMessages([
                'referral_code' => __('referrals::app.validation.company_required'),
            ]);
        }

        return DB::transaction(function () use ($companyId, $partner): ReferralCode {
            Partner::withoutGlobalScopes()->whereKey($partner->id)->lockForUpdate()->firstOrFail();

            return ReferralCode::query()->firstOrCreate(
                ['company_id' => $companyId, 'partner_id' => $partner->id],
                ['code' => ReferralCode::generateUniqueCode(), 'is_active' => true],
            );
        }, 3);
    }

    public function applyToDraftMove(
        Move $move,
        ?string $rawCode,
        string $context,
        ?string $sourceType = null,
        ?int $sourceId = null,
    ): ?ReferralRedemption {
        $codeValue = Str::upper(trim((string) $rawCode));

        if ($codeValue === '') {
            return null;
        }

        if ($move->state !== MoveState::DRAFT) {
            throw ValidationException::withMessages([
                'referral_code' => __('referrals::app.validation.draft_only'),
            ]);
        }

        return DB::transaction(function () use ($move, $codeValue, $context, $sourceType, $sourceId): ReferralRedemption {
            $move->refresh()->loadMissing(['invoiceLines', 'partner']);

            if ($existing = ReferralRedemption::query()->where('move_id', $move->id)->first()) {
                if ($existing->referralCode?->code !== $codeValue) {
                    throw ValidationException::withMessages([
                        'referral_code' => __('referrals::app.validation.already_applied'),
                    ]);
                }

                if ($existing->status === RedemptionStatus::Void && ! $existing->reward_move_id) {
                    $this->assertCampaignUsageAllowed(
                        $existing->campaign,
                        $move,
                        $existing->snapshot['product_ids'] ?? [],
                    );

                    $existing->update([
                        'status'             => RedemptionStatus::Pending,
                        'reversed_at'        => null,
                        'first_purchase_key' => $existing->campaign?->first_purchase_only
                            ? $existing->campaign_id.':'.$existing->referred_partner_id
                            : null,
                    ]);
                }

                return $existing->refresh();
            }

            $code = ReferralCode::query()
                ->where('code', $codeValue)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (! $code || (int) $code->company_id !== (int) $move->company_id) {
                throw ValidationException::withMessages([
                    'referral_code' => __('referrals::app.validation.invalid_code'),
                ]);
            }

            if ((int) $code->partner_id === (int) $move->partner_id) {
                throw ValidationException::withMessages([
                    'referral_code' => __('referrals::app.validation.self_referral'),
                ]);
            }

            $productAmounts = $move->invoiceLines
                ->filter(fn (MoveLine $line): bool => filled($line->product_id) && (float) $line->quantity > 0 && (float) $line->price_unit > 0)
                ->groupBy(fn (MoveLine $line): int => (int) $line->product_id)
                ->map(fn ($lines): float => (float) $lines->sum(fn (MoveLine $line): float => $this->lineGrossAfterExistingDiscount($line)));

            $campaign = $this->resolveCampaign($move, $code, $context, $productAmounts->all());
            $eligibleProductIds = $campaign->products->pluck('id')->map(fn ($id): int => (int) $id)->all();
            $eligibleLines = $move->invoiceLines
                ->filter(fn (MoveLine $line): bool => in_array((int) $line->product_id, $eligibleProductIds, true))
                ->values();
            $eligibleAmount = (float) $eligibleLines->sum(fn (MoveLine $line): float => $this->lineGrossAfterExistingDiscount($line));
            $discount = $campaign->discount_type === DiscountType::Percentage
                ? round($eligibleAmount * ((float) $campaign->customer_discount / 100), 4)
                : min((float) $campaign->customer_discount, $eligibleAmount);

            if ($eligibleAmount < (float) $campaign->minimum_eligible_amount || $discount <= 0) {
                throw ValidationException::withMessages([
                    'referral_code' => __('referrals::app.validation.minimum_not_met'),
                ]);
            }

            $this->assertCampaignUsageAllowed($campaign, $move, $eligibleProductIds);
            $this->allocateFixedDiscount($eligibleLines, $discount);

            $move->forceFill(['referral_code' => $codeValue])->save();
            AccountFacade::computeAccountMove($move->refresh());

            return ReferralRedemption::query()->create([
                'campaign_id'         => $campaign->id,
                'referral_code_id'    => $code->id,
                'company_id'          => $move->company_id,
                'currency_id'         => $move->currency_id,
                'referrer_partner_id' => $code->partner_id,
                'referred_partner_id' => $move->partner_id,
                'move_id'             => $move->id,
                'first_purchase_key'  => $campaign->first_purchase_only
                    ? $campaign->id.':'.$move->partner_id
                    : null,
                'source_type'       => $sourceType,
                'source_id'         => $sourceId,
                'context'           => $context,
                'status'            => RedemptionStatus::Pending,
                'eligible_amount'   => $eligibleAmount,
                'customer_discount' => $discount,
                'referrer_reward'   => $campaign->referrer_reward,
                'snapshot'          => [
                    'campaign_name'                => $campaign->name,
                    'code'                         => $codeValue,
                    'discount_type'                => $campaign->discount_type->value,
                    'configured_customer_discount' => (float) $campaign->customer_discount,
                    'product_ids'                  => $eligibleProductIds,
                ],
            ]);
        }, 3);
    }

    /**
     * @param  array<int, float>  $productAmounts
     */
    private function resolveCampaign(Move $move, ReferralCode $code, string $context, array $productAmounts): ReferralCampaign
    {
        $campaigns = ReferralCampaign::query()
            ->active()
            ->where('company_id', $move->company_id)
            ->where('currency_id', $move->currency_id)
            ->with('products:id')
            ->lockForUpdate()
            ->get()
            ->filter(fn (ReferralCampaign $campaign): bool => in_array($context, $campaign->contexts ?? [], true))
            ->filter(function (ReferralCampaign $campaign) use ($productAmounts): bool {
                $eligibleAmount = $campaign->products->sum(fn ($product): float => (float) ($productAmounts[(int) $product->id] ?? 0));

                return $eligibleAmount >= (float) $campaign->minimum_eligible_amount && $eligibleAmount > 0;
            })
            ->sortByDesc(function (ReferralCampaign $campaign) use ($productAmounts): float {
                $eligibleAmount = $campaign->products->sum(fn ($product): float => (float) ($productAmounts[(int) $product->id] ?? 0));

                return $campaign->discount_type === DiscountType::Percentage
                    ? $eligibleAmount * ((float) $campaign->customer_discount / 100)
                    : min((float) $campaign->customer_discount, $eligibleAmount);
            });

        $campaign = $campaigns->first();

        if (! $campaign) {
            throw ValidationException::withMessages([
                'referral_code' => __('referrals::app.validation.no_campaign'),
            ]);
        }

        return $campaign;
    }

    /**
     * @param  array<int>  $eligibleProductIds
     */
    private function assertCampaignUsageAllowed(ReferralCampaign $campaign, Move $move, array $eligibleProductIds): void
    {
        if ($campaign->max_redemptions !== null) {
            $used = ReferralRedemption::query()
                ->where('campaign_id', $campaign->id)
                ->whereIn('status', [RedemptionStatus::Pending->value, RedemptionStatus::Earned->value])
                ->count();

            if ($used >= $campaign->max_redemptions) {
                throw ValidationException::withMessages([
                    'referral_code' => __('referrals::app.validation.campaign_limit'),
                ]);
            }
        }

        if (! $campaign->first_purchase_only) {
            return;
        }

        $alreadyRedeemed = ReferralRedemption::query()
            ->where('campaign_id', $campaign->id)
            ->where('referred_partner_id', $move->partner_id)
            ->whereIn('status', [RedemptionStatus::Pending->value, RedemptionStatus::Earned->value])
            ->exists();

        $historicalPurchase = Move::query()
            ->where('id', '!=', $move->id)
            ->where('company_id', $move->company_id)
            ->where('partner_id', $move->partner_id)
            ->where('state', MoveState::POSTED)
            ->whereHas('invoiceLines', fn ($query) => $query->whereIn('product_id', $eligibleProductIds))
            ->exists();

        if ($alreadyRedeemed || $historicalPurchase) {
            throw ValidationException::withMessages([
                'referral_code' => __('referrals::app.validation.first_purchase_only'),
            ]);
        }
    }

    private function lineGrossAfterExistingDiscount(MoveLine $line): float
    {
        $gross = (float) $line->quantity * (float) $line->price_unit;

        return max(0, $gross * (1 - ((float) $line->discount / 100)));
    }

    private function allocateFixedDiscount($lines, float $discount): void
    {
        $remaining = $discount;

        foreach ($lines as $line) {
            if ($remaining <= 0.0001) {
                break;
            }

            $grossBeforeAnyDiscount = (float) $line->quantity * (float) $line->price_unit;
            $currentNet = $this->lineGrossAfterExistingDiscount($line);
            $allocation = min($currentNet, $remaining);
            $newNet = $currentNet - $allocation;
            $effectivePercentage = $grossBeforeAnyDiscount > 0
                ? round((1 - ($newNet / $grossBeforeAnyDiscount)) * 100, 8)
                : 0;

            $line->update(['discount' => min(100, $effectivePercentage)]);
            $remaining -= $allocation;
        }

        if ($remaining > 0.01) {
            throw ValidationException::withMessages([
                'referral_code' => __('referrals::app.validation.discount_too_large'),
            ]);
        }
    }
}
