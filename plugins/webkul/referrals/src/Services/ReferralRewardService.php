<?php

declare(strict_types=1);

namespace Webkul\Referral\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\MoveWorkflow;
use Webkul\Account\Services\Reconciler;
use Webkul\Referral\Enums\RedemptionStatus;
use Webkul\Referral\Models\ReferralRedemption;
use Webkul\Referral\Models\ReferralWalletTransaction;

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
            app(ReferralWalletService::class)->recordEarn($redemption, $rewardMove);
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
            app(ReferralWalletService::class)->recordEarn($redemption, $reversalMove, reverse: true);
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
        $defaults = app(ReferralAccountingDefaults::class)->ensureForCompany($redemption->company_id);

        $amount = (float) $redemption->referrer_reward;
        $userId = Auth::guard('web')->id();
        $entry = Move::query()->create([
            'move_type'   => MoveType::ENTRY,
            'state'       => MoveState::DRAFT,
            'journal_id'  => $defaults->journal_id,
            'company_id'  => $redemption->company_id,
            'currency_id' => $redemption->currency_id,
            'date'        => now()->toDateString(),
            'reference'   => ($reverse ? 'Referral reward reversal #' : 'Referral reward #').$redemption->id,
            'creator_id'  => $userId,
        ]);

        $entry->lines()->create([
            'name'            => $reverse ? 'Referral discount expense reversal' : 'Referral discount expense',
            'account_id'      => $defaults->expense_account_id,
            'debit'           => $reverse ? 0 : $amount,
            'credit'          => $reverse ? $amount : 0,
            'balance'         => $reverse ? -$amount : $amount,
            'amount_currency' => $reverse ? -$amount : $amount,
            'currency_id'     => $redemption->currency_id,
            'creator_id'      => $userId,
        ]);

        $entry->lines()->create([
            'name'                     => $reverse ? 'Referral rewards payable reversal' : 'Referral rewards payable',
            'account_id'               => $defaults->liability_account_id,
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
        $liabilityAccountId = app(ReferralAccountingDefaults::class)
            ->ensureForCompany($redemption->company_id)->liability_account_id;
        $walletEarnMoveId = ReferralWalletTransaction::query()
            ->where('referral_redemption_id', $redemption->id)
            ->where('type', 'earn')
            ->value('accounting_move_id');
        $receivableLines = Move::query()
            ->whereIn('id', array_filter([$redemption->reward_move_id, $walletEarnMoveId, $reversalMove->id]))
            ->with(['lines.account'])
            ->get()
            ->flatMap->lines
            ->filter(fn ($line): bool => (int) $line->partner_id === (int) $redemption->referrer_partner_id
                && (int) $line->account_id === (int) $liabilityAccountId
                && ! $line->reconciled)
            ->values();

        if ($receivableLines->count() >= 2) {
            app(Reconciler::class)->reconcile($receivableLines);
        }
    }
}
