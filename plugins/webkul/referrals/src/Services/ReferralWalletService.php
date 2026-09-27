<?php

declare(strict_types=1);

namespace Webkul\Referral\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Services\MoveWorkflow;
use Webkul\Account\Services\Reconciler;
use Webkul\Referral\Models\ReferralRedemption;
use Webkul\Referral\Models\ReferralWalletBalance;
use Webkul\Referral\Models\ReferralWalletTransaction;
use Webkul\Support\Models\Scopes\CompaniesScope;

class ReferralWalletService
{
    public const MAX_INVOICE_USAGE_PERCENT = 50.0;

    public const FULL_USAGE_PERCENT = 100.0;

    public function importLegacyEarnedRewards(): int
    {
        $count = 0;
        ReferralRedemption::query()
            ->where('status', 'earned')
            ->whereNotNull('reward_move_id')
            ->orderBy('id')
            ->each(function (ReferralRedemption $redemption) use (&$count): void {
                if ($this->importLegacyEarnedReward($redemption)) {
                    $count++;
                }
            });

        return $count;
    }

    public function importLegacyEarnedReward(ReferralRedemption $redemption): bool
    {
        return DB::transaction(function () use ($redemption): bool {
            $redemption = ReferralRedemption::query()->lockForUpdate()->findOrFail($redemption->id);
            $key = 'earn:'.$redemption->id;
            if (ReferralWalletTransaction::query()->where('idempotency_key', $key)->exists()) {
                return false;
            }

            $legacyCredit = MoveLine::query()
                ->where('move_id', $redemption->reward_move_id)
                ->where('partner_id', $redemption->referrer_partner_id)
                ->where('credit', '>', 0)
                ->whereHas('account', fn ($query) => $query->where('account_type', AccountType::ASSET_RECEIVABLE))
                ->first();

            if (! $legacyCredit) {
                return false;
            }

            $rewardAmount = (float) $redemption->referrer_reward;
            $amount = abs((float) $legacyCredit->amount_residual);
            $entry = null;

            if ($amount > 0) {
                $defaults = app(ReferralAccountingDefaults::class)->ensureForCompany($redemption->company_id);
                $entry = Move::query()->create([
                    'move_type'  => MoveType::ENTRY, 'state' => MoveState::DRAFT, 'journal_id' => $defaults->journal_id,
                    'company_id' => $redemption->company_id, 'currency_id' => $redemption->currency_id,
                    'partner_id' => $redemption->referrer_partner_id,
                    'date'       => now()->toDateString(), 'reference' => 'Legacy referral reward reclassification #'.$redemption->id,
                    'creator_id' => Auth::id(),
                ]);
                $common = [
                    'partner_id'  => $redemption->referrer_partner_id,
                    'currency_id' => $redemption->currency_id,
                    'creator_id'  => Auth::id(),
                ];
                $entry->lines()->create($common + [
                    'name'            => 'Legacy referral customer credit reclassification', 'account_id' => $legacyCredit->account_id,
                    'debit'           => $amount, 'credit' => 0, 'balance' => $amount, 'amount_currency' => $amount,
                    'amount_residual' => $amount, 'amount_residual_currency' => $amount,
                ]);
                $entry->lines()->create($common + [
                    'name'            => 'Referral rewards payable', 'account_id' => $defaults->liability_account_id,
                    'debit'           => 0, 'credit' => $amount, 'balance' => -$amount, 'amount_currency' => -$amount,
                    'amount_residual' => -$amount, 'amount_residual_currency' => -$amount,
                ]);
                $entry = app(MoveWorkflow::class)->post($entry->refresh());

                app(Reconciler::class)->reconcile(new EloquentCollection([
                    $legacyCredit->refresh(),
                    $entry->lines()->where('account_id', $legacyCredit->account_id)->firstOrFail(),
                ]));
            }

            $balance = $this->lockedBalance($redemption->company_id, $redemption->currency_id, $redemption->referrer_partner_id);
            $balance->available_amount = (float) $balance->available_amount + $amount;
            $balance->lifetime_earned = (float) $balance->lifetime_earned + $rewardAmount;
            $balance->save();

            ReferralWalletTransaction::query()->create([
                'company_id'         => $redemption->company_id, 'currency_id' => $redemption->currency_id,
                'partner_id'         => $redemption->referrer_partner_id, 'referral_redemption_id' => $redemption->id,
                'accounting_move_id' => $entry?->id, 'type' => 'earn', 'amount' => $amount,
                'idempotency_key'    => $key, 'metadata' => [
                    'legacy_reclassified' => true, 'original_reward_amount' => $rewardAmount,
                ], 'creator_id' => Auth::id(),
            ]);

            return true;
        }, 3);
    }

    public function balance(int $companyId, int $currencyId, int $partnerId): float
    {
        return (float) ReferralWalletBalance::query()
            ->where('company_id', $companyId)->where('currency_id', $currencyId)->where('partner_id', $partnerId)
            ->value('available_amount');
    }

    public function recordEarn(ReferralRedemption $redemption, Move $accountingMove, bool $reverse = false): void
    {
        $key = ($reverse ? 'earn-reversal:' : 'earn:').$redemption->id;
        if (ReferralWalletTransaction::query()->where('idempotency_key', $key)->exists()) {
            return;
        }

        $balance = $this->lockedBalance($redemption->company_id, $redemption->currency_id, $redemption->referrer_partner_id);
        $amount = (float) $redemption->referrer_reward;

        if ($reverse && (float) $balance->available_amount < $amount) {
            throw new RuntimeException(__('referrals::app.validation.reward_already_used'));
        }

        $balance->available_amount = (float) $balance->available_amount + ($reverse ? -$amount : $amount);
        if (! $reverse) {
            $balance->lifetime_earned = (float) $balance->lifetime_earned + $amount;
        }
        $balance->save();

        ReferralWalletTransaction::query()->create([
            'company_id'         => $redemption->company_id, 'currency_id' => $redemption->currency_id,
            'partner_id'         => $redemption->referrer_partner_id, 'referral_redemption_id' => $redemption->id,
            'accounting_move_id' => $accountingMove->id, 'type' => $reverse ? 'earn_reversal' : 'earn',
            'amount'             => $reverse ? -$amount : $amount, 'idempotency_key' => $key, 'creator_id' => Auth::id(),
        ]);
    }

    public function applyToPostedInvoice(Move $invoice): ?ReferralWalletTransaction
    {
        if ($invoice->move_type !== MoveType::OUT_INVOICE) {
            return null;
        }

        return DB::transaction(function () use ($invoice): ?ReferralWalletTransaction {
            $key = 'redeem:'.$invoice->id;
            $existing = ReferralWalletTransaction::query()->where('idempotency_key', $key)->first();
            if ($existing) {
                return $existing;
            }

            $invoice->refresh();
            $requestedAmount = round((float) $invoice->referral_wallet_amount, 4);
            $maximumAllowed = $this->maximumUsableForInvoice($invoice);
            if ($maximumAllowed <= 0) {
                if ($requestedAmount > 0) {
                    throw new RuntimeException(__('referrals::app.validation.wallet_not_eligible'));
                }

                return null;
            }

            $availableAmount = $this->balance($invoice->company_id, $invoice->currency_id, $invoice->partner_id);
            if ($requestedAmount <= 0 && $availableAmount <= 0) {
                return null;
            }

            $balance = $this->lockedBalance($invoice->company_id, $invoice->currency_id, $invoice->partner_id);
            $amount = $requestedAmount > 0
                ? $requestedAmount
                : min($maximumAllowed, (float) $invoice->amount_residual, (float) $balance->available_amount);

            if ($amount <= 0) {
                return null;
            }

            if ($amount > $maximumAllowed) {
                throw new RuntimeException(__('referrals::app.validation.wallet_limit_exceeded', [
                    'amount' => number_format($maximumAllowed, 2),
                ]));
            }

            if ($amount > (float) $invoice->amount_residual) {
                throw new RuntimeException(__('referrals::app.validation.wallet_exceeds_invoice'));
            }

            if ($amount > (float) $balance->available_amount) {
                throw new RuntimeException(__('referrals::app.validation.wallet_insufficient'));
            }

            $invoice->forceFill(['referral_wallet_amount' => $amount])->save();

            $defaults = app(ReferralAccountingDefaults::class)->ensureForCompany($invoice->company_id);
            $receivableId = $this->receivableAccountId($invoice);
            $settlement = $this->settlementEntry($invoice, $amount, $defaults->journal_id, $defaults->liability_account_id, $receivableId);

            $receivableLines = $invoice->lines()->where('account_id', $receivableId)->get()
                ->merge($settlement->lines()->where('account_id', $receivableId)->get())
                ->reject->reconciled->values();
            app(Reconciler::class)->reconcile(new EloquentCollection($receivableLines->all()));

            $liabilityLines = MoveLine::query()
                ->where('account_id', $defaults->liability_account_id)
                ->where('partner_id', $invoice->partner_id)
                ->where('company_id', $invoice->company_id)
                ->where('reconciled', false)
                ->whereHas('move', fn ($query) => $query->where('state', MoveState::POSTED))
                ->get();
            app(Reconciler::class)->reconcile(new EloquentCollection($liabilityLines->all()));

            $balance->available_amount = (float) $balance->available_amount - $amount;
            $balance->lifetime_redeemed = (float) $balance->lifetime_redeemed + $amount;
            $balance->save();

            return ReferralWalletTransaction::query()->create([
                'company_id'      => $invoice->company_id, 'currency_id' => $invoice->currency_id,
                'partner_id'      => $invoice->partner_id, 'accounting_move_id' => $settlement->id,
                'invoice_move_id' => $invoice->id, 'type' => 'redeem', 'amount' => -$amount,
                'idempotency_key' => $key, 'creator_id' => Auth::id(),
            ]);
        }, 3);
    }

    public function maximumUsableForInvoice(Move $invoice): float
    {
        $invoice->loadMissing('invoiceLines');

        $fullCoverageProductIds = Schema::hasTable('wifi_packages')
            ? DB::table('wifi_packages')->pluck('product_id')->map(fn ($id): int => (int) $id)->all()
            : [];

        if (Schema::hasTable('online_system_plans')) {
            $onlineProductIds = DB::table('online_system_plans')
                ->whereNotNull('product_id')->pluck('product_id')->map(fn ($id): int => (int) $id)->all();
            $fullCoverageProductIds = array_values(array_unique([...$fullCoverageProductIds, ...$onlineProductIds]));
        }

        $maximum = $invoice->invoiceLines->sum(function (MoveLine $line) use ($fullCoverageProductIds): float {
            $percentage = in_array((int) $line->product_id, $fullCoverageProductIds, true)
                ? self::FULL_USAGE_PERCENT
                : self::MAX_INVOICE_USAGE_PERCENT;

            return (float) $line->price_subtotal * ($percentage / 100);
        });

        return round(max(0, (float) $maximum), 4);
    }

    public function reverseInvoiceUsage(Move $invoice): void
    {
        DB::transaction(function () use ($invoice): void {
            $usage = ReferralWalletTransaction::query()
                ->where('invoice_move_id', $invoice->id)->where('type', 'redeem')->lockForUpdate()->first();
            if (! $usage || ReferralWalletTransaction::query()->where('idempotency_key', 'redeem-reversal:'.$invoice->id)->exists()) {
                return;
            }

            $reversal = null;
            $settlement = Move::query()->with('lines.matchedDebits', 'lines.matchedCredits')->find($usage->accounting_move_id);
            if ($settlement) {
                $reconciler = app(Reconciler::class);
                $settlement->lines->each(function ($line) use ($reconciler): void {
                    $line->matchedDebits->each(fn ($partial) => $reconciler->unReconcile($partial));
                    $line->matchedCredits()->get()->each(fn ($partial) => $reconciler->unReconcile($partial));

                    $line->refresh();
                    if ($line->reconciled && ! $line->matchedDebits()->exists() && ! $line->matchedCredits()->exists()) {
                        $line->update([
                            'reconciled'               => false,
                            'amount_residual'          => $line->balance,
                            'amount_residual_currency' => $line->amount_currency,
                        ]);
                    }
                });
                $reversal = app(MoveWorkflow::class)->reverse(collect([$settlement]), [[
                    'date' => now()->toDateString(), 'reference' => 'Referral wallet reversal #'.$invoice->id,
                ]], true)->first();
            }

            $amount = abs((float) $usage->amount);
            $balance = $this->lockedBalance($usage->company_id, $usage->currency_id, $usage->partner_id);
            $balance->available_amount = (float) $balance->available_amount + $amount;
            $balance->save();

            ReferralWalletTransaction::query()->create([
                'company_id'         => $usage->company_id, 'currency_id' => $usage->currency_id, 'partner_id' => $usage->partner_id,
                'accounting_move_id' => $reversal?->id, 'invoice_move_id' => $invoice->id, 'type' => 'redeem_reversal',
                'amount'             => $amount, 'idempotency_key' => 'redeem-reversal:'.$invoice->id, 'creator_id' => Auth::id(),
            ]);
        }, 3);
    }

    private function lockedBalance(int $companyId, int $currencyId, int $partnerId): ReferralWalletBalance
    {
        DB::table('referral_wallet_balances')->insertOrIgnore([
            'company_id'        => $companyId,
            'currency_id'       => $currencyId,
            'partner_id'        => $partnerId,
            'available_amount'  => 0,
            'lifetime_earned'   => 0,
            'lifetime_redeemed' => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return ReferralWalletBalance::query()
            ->where('company_id', $companyId)->where('currency_id', $currencyId)->where('partner_id', $partnerId)
            ->lockForUpdate()->firstOrFail();
    }

    private function receivableAccountId(Move $invoice): int
    {
        $partner = Partner::query()->findOrFail($invoice->partner_id);
        $id = $partner->companyPropertyValue('property_account_receivable_id', $invoice->company_id)
            ?: Account::withoutGlobalScope(CompaniesScope::class)->where('account_type', AccountType::ASSET_RECEIVABLE)
                ->where('deprecated', false)->where(owned_by_company($invoice->company_id))->value('id');

        throw_unless($id, RuntimeException::class, __('referrals::app.validation.receivable_account_required'));

        return (int) $id;
    }

    private function settlementEntry(Move $invoice, float $amount, int $journalId, int $liabilityId, int $receivableId): Move
    {
        $entry = Move::query()->create([
            'move_type'  => MoveType::ENTRY, 'state' => MoveState::DRAFT, 'journal_id' => $journalId,
            'company_id' => $invoice->company_id, 'currency_id' => $invoice->currency_id,
            'partner_id' => $invoice->partner_id,
            'date'       => now()->toDateString(), 'reference' => 'Referral wallet used on '.$invoice->name,
            'creator_id' => Auth::id(),
        ]);
        $common = ['partner_id' => $invoice->partner_id, 'currency_id' => $invoice->currency_id, 'creator_id' => Auth::id()];
        $entry->lines()->create($common + [
            'name'            => 'Referral reward liability settlement', 'account_id' => $liabilityId,
            'debit'           => $amount, 'credit' => 0, 'balance' => $amount, 'amount_currency' => $amount,
            'amount_residual' => $amount, 'amount_residual_currency' => $amount,
        ]);
        $entry->lines()->create($common + [
            'name'            => 'Referral credit applied to invoice', 'account_id' => $receivableId,
            'debit'           => 0, 'credit' => $amount, 'balance' => -$amount, 'amount_currency' => -$amount,
            'amount_residual' => -$amount, 'amount_residual_currency' => -$amount,
        ]);

        return app(MoveWorkflow::class)->post($entry->refresh());
    }
}
