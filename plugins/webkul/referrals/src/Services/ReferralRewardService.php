<?php

declare(strict_types=1);

namespace Webkul\Referral\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\Account\Services\MoveWorkflow;
use Webkul\Account\Services\Reconciler;
use Webkul\Referral\Enums\RedemptionStatus;
use Webkul\Referral\Models\ReferralRedemption;
use Webkul\Support\Models\Scopes\CompaniesScope;

class ReferralRewardService
{
    public function earnForPaidMove(Move $move): ?ReferralRedemption
    {
        $move->refresh();

        if ($move->payment_state !== PaymentState::PAID) {
            return null;
        }

        return DB::transaction(function () use ($move): ?ReferralRedemption {
            $redemption = ReferralRedemption::query()
                ->with(['campaign', 'referrer'])
                ->where('move_id', $move->id)
                ->lockForUpdate()
                ->first();

            if (! $redemption || $redemption->status !== RedemptionStatus::Pending) {
                return $redemption;
            }

            $reward = (float) $redemption->referrer_reward;
            if ($reward <= 0) {
                $redemption->update(['status' => RedemptionStatus::Earned, 'earned_at' => now()]);

                return $redemption->refresh();
            }

            $rewardMove = $this->createRewardEntry($redemption, reverse: false);
            $redemption->update([
                'reward_move_id' => $rewardMove->id,
                'status'         => RedemptionStatus::Earned,
                'earned_at'      => now(),
            ]);

            return $redemption->refresh();
        }, 3);
    }

    public function reverseForMove(Move $move): ?ReferralRedemption
    {
        return DB::transaction(function () use ($move): ?ReferralRedemption {
            $redemption = ReferralRedemption::query()
                ->with(['campaign', 'referrer'])
                ->where('move_id', $move->id)
                ->lockForUpdate()
                ->first();

            if (! $redemption) {
                return null;
            }

            if ($redemption->status === RedemptionStatus::Pending) {
                $redemption->update([
                    'status'             => RedemptionStatus::Void,
                    'first_purchase_key' => null,
                    'reversed_at'        => now(),
                ]);

                return $redemption->refresh();
            }

            if ($redemption->status !== RedemptionStatus::Earned) {
                return $redemption;
            }

            $reversalMove = $this->createRewardEntry($redemption, reverse: true);
            $this->reconcileRewardReversal($redemption, $reversalMove);
            $redemption->update([
                'reward_reversal_move_id' => $reversalMove->id,
                'status'                  => RedemptionStatus::Reversed,
                'reversed_at'             => now(),
            ]);

            return $redemption->refresh();
        }, 3);
    }

    private function createRewardEntry(ReferralRedemption $redemption, bool $reverse): Move
    {
        $campaign = $redemption->campaign;
        $partner = Partner::query()->findOrFail($redemption->referrer_partner_id);
        $receivableAccountId = $partner->companyPropertyValue('property_account_receivable_id', $redemption->company_id)
            ?: Account::withoutGlobalScope(CompaniesScope::class)
                ->where('account_type', AccountType::ASSET_RECEIVABLE)
                ->where('deprecated', false)
                ->where(function ($query) use ($redemption): void {
                    $query->whereHas('companies', fn ($companyQuery) => $companyQuery->where('companies.id', $redemption->company_id))
                        ->orWhereDoesntHave('companies');
                })
                ->value('id');

        if (! $receivableAccountId) {
            throw new RuntimeException(__('referrals::app.validation.receivable_account_required'));
        }

        $amount = (float) $redemption->referrer_reward;
        $userId = Auth::guard('web')->id();
        $entry = Move::query()->create([
            'move_type'   => MoveType::ENTRY,
            'state'       => MoveState::DRAFT,
            'journal_id'  => $campaign->journal_id,
            'company_id'  => $redemption->company_id,
            'currency_id' => $redemption->currency_id,
            'date'        => now()->toDateString(),
            'reference'   => ($reverse ? 'Referral reward reversal #' : 'Referral reward #').$redemption->id,
            'creator_id'  => $userId,
        ]);

        $entry->lines()->create([
            'name'            => $reverse ? 'Referral marketing expense reversal' : 'Referral marketing expense',
            'account_id'      => $campaign->expense_account_id,
            'debit'           => $reverse ? 0 : $amount,
            'credit'          => $reverse ? $amount : 0,
            'balance'         => $reverse ? -$amount : $amount,
            'amount_currency' => $reverse ? -$amount : $amount,
            'currency_id'     => $redemption->currency_id,
            'creator_id'      => $userId,
        ]);

        $entry->lines()->create([
            'name'                     => $reverse ? 'Referral customer credit reversal' : 'Referral customer credit',
            'account_id'               => $receivableAccountId,
            'partner_id'               => $redemption->referrer_partner_id,
            'debit'                    => $reverse ? $amount : 0,
            'credit'                   => $reverse ? 0 : $amount,
            'balance'                  => $reverse ? $amount : -$amount,
            'amount_currency'          => $reverse ? $amount : -$amount,
            'amount_residual'          => $reverse ? $amount : -$amount,
            'amount_residual_currency' => $reverse ? $amount : -$amount,
            'currency_id'              => $redemption->currency_id,
            'creator_id'               => $userId,
        ]);

        return app(MoveWorkflow::class)->post($entry->refresh());
    }

    private function reconcileRewardReversal(ReferralRedemption $redemption, Move $reversalMove): void
    {
        $receivableLines = Move::query()
            ->whereIn('id', [$redemption->reward_move_id, $reversalMove->id])
            ->with(['lines.account'])
            ->get()
            ->flatMap->lines
            ->filter(fn ($line): bool => (int) $line->partner_id === (int) $redemption->referrer_partner_id
                && $line->account?->account_type === AccountType::ASSET_RECEIVABLE
                && ! $line->reconciled)
            ->values();

        if ($receivableLines->count() >= 2) {
            app(Reconciler::class)->reconcile($receivableLines);
        }
    }
}
