<?php

namespace Webkul\SoftwareOnline\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Events\MovePaid;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Services\MoveWorkflow;
use Webkul\Account\Services\Reconciler;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;
use Webkul\Referral\Services\ReferralService;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Enums\TransactionType;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineInstanceTransaction;
use Webkul\SoftwareOnline\Models\OnlineSystemPlan;
use Webkul\Support\Models\Company;

class OnlineBillingService
{
    /**
     * Get available customer credit balance
     */
    public function getAvailableBalance(Partner $partner): float
    {
        $companyId = (int) ($partner->company_id ?: current_company_id());
        $currencyId = Company::query()->whereKey($companyId)->value('currency_id');

        if (! $companyId || ! $currencyId) {
            return 0.0;
        }

        $balance = (float) MoveLine::query()
            ->where('partner_id', $partner->id)
            ->where('company_id', $companyId)
            ->where('currency_id', $currencyId)
            ->where('parent_state', MoveState::POSTED)
            ->where('reconciled', false)
            ->where('balance', '<', 0)
            ->where('amount_residual', '<', 0)
            ->whereHas('account', fn ($query) => $query->where('account_type', AccountType::ASSET_RECEIVABLE))
            ->sum('amount_residual');

        return abs($balance);
    }

    /**
     * Check if partner has enough balance for amount
     */
    /**
     * Check if partner has already used their one-time free trial
     */
    public function hasUsedTrial(Partner $partner): bool
    {
        return OnlineInstance::query()
            ->where('partner_id', $partner->id)
            ->where('billing_cycle', BillingCycle::Trial->value)
            ->exists()
            || OnlineInstanceTransaction::query()
                ->where('partner_id', $partner->id)
                ->where('billing_cycle', BillingCycle::Trial->value)
                ->exists();
    }

    /**
     * Check if partner has enough balance for amount
     */
    public function hasSufficientBalance(Partner $partner, float $amount): bool
    {
        return $this->getAvailableBalance($partner) >= $amount;
    }

    /**
     * Process new website subscription from customer balance or free trial
     */
    public function subscribeNewInstance(
        Partner $partner,
        OnlineSystemPlan $plan,
        string $name,
        ?string $subdomain,
        BillingCycle $cycle,
        ?string $adminEmail = null,
        ?string $adminUsername = null,
        ?string $referralCode = null,
    ): OnlineInstance {
        if ($cycle === BillingCycle::Trial) {
            if ($this->hasUsedTrial($partner)) {
                throw new Exception(__('software-online::filament/customer/pages/explore.trial_already_used'));
            }
            $price = 0.00;
        } elseif ($cycle === BillingCycle::Annual) {
            $price = (float) $plan->annual_price;
        } else {
            $price = (float) $plan->monthly_price;
        }

        return DB::transaction(function () use ($partner, $plan, $name, $subdomain, $cycle, $price, $adminEmail, $adminUsername, $referralCode) {
            $startsAt = now();
            if ($cycle === BillingCycle::Trial) {
                $trialDays = $plan->trial_days > 0 ? $plan->trial_days : 14;
                $expiresAt = now()->addDays($trialDays);
            } elseif ($cycle === BillingCycle::Annual) {
                $expiresAt = now()->addYear();
            } else {
                $expiresAt = now()->addMonth();
            }

            $instance = OnlineInstance::create([
                'partner_id'      => $partner->id,
                'system_id'       => $plan->system_id,
                'plan_id'         => $plan->id,
                'name'            => $name,
                'subdomain'       => $subdomain,
                'admin_email'     => $adminEmail ?? $partner->email,
                'admin_username'  => $adminUsername ?? 'admin',
                'billing_cycle'   => $cycle,
                'price'           => $price,
                'status'          => InstanceStatus::Pending,
                'starts_at'       => $startsAt,
                'expires_at'      => $expiresAt,
                'last_renewed_at' => $startsAt,
                'auto_renew'      => true,
            ]);

            // Generate invoice move only if price > 0
            $moveData = $price > 0
                ? $this->createSubscriptionInvoice($instance, $plan, $partner, $price, $cycle, 'new_subscription', $referralCode)
                : null;

            if ($moveData) {
                $instance->update(['move_id' => $moveData['move']->id]);
            }

            // Record transaction
            OnlineInstanceTransaction::create([
                'instance_id'   => $instance->id,
                'partner_id'    => $partner->id,
                'type'          => TransactionType::NewSubscription,
                'billing_cycle' => $cycle,
                'amount'        => $moveData ? (float) $moveData['move']->amount_total : $price,
                'status'        => 'paid',
                'period_start'  => $startsAt->toDateString(),
                'period_end'    => $expiresAt->toDateString(),
                'move_id'       => $moveData['move']->id ?? null,
                'move_line_id'  => $moveData['line']->id ?? null,
            ]);

            // Trigger API Provisioning
            app(OnlineSystemProvisioningService::class)->provisionInstance($instance);

            return $instance;
        });
    }

    /**
     * Renew an existing instance subscription from customer balance
     */
    public function renewInstance(OnlineInstance $instance, ?BillingCycle $cycle = null): bool
    {
        $partner = $instance->partner;
        $plan = $instance->plan;

        // Renewal is only allowed as Monthly or Annual (never Trial)
        $cycle = $cycle ?? $instance->billing_cycle ?? BillingCycle::Monthly;
        if ($cycle === BillingCycle::Trial) {
            $cycle = BillingCycle::Monthly;
        }

        $price = $cycle === BillingCycle::Annual ? (float) $plan->annual_price : (float) $plan->monthly_price;

        if ($price > 0 && ! $this->hasSufficientBalance($partner, (float) $price)) {
            throw new Exception(__('software-online::filament/customer/pages/explore.insufficient_balance'));
        }

        return DB::transaction(function () use ($instance, $plan, $partner, $cycle, $price) {
            $currentExpiry = ($instance->expires_at && $instance->expires_at->isFuture())
                ? $instance->expires_at
                : now();

            $newExpiry = $cycle === BillingCycle::Annual
                ? (clone $currentExpiry)->addYear()
                : (clone $currentExpiry)->addMonth();

            // Generate renewal invoice move
            $moveData = $this->createSubscriptionInvoice($instance, $plan, $partner, $price, $cycle, 'renewal');

            $instance->update([
                'billing_cycle'   => $cycle,
                'price'           => $price,
                'expires_at'      => $newExpiry,
                'last_renewed_at' => now(),
                'status'          => InstanceStatus::Active,
                'move_id'         => $moveData['move']->id ?? $instance->move_id,
            ]);

            // Record renewal transaction
            OnlineInstanceTransaction::create([
                'instance_id'   => $instance->id,
                'partner_id'    => $partner->id,
                'type'          => TransactionType::Renewal,
                'billing_cycle' => $cycle,
                'amount'        => $price,
                'status'        => 'paid',
                'period_start'  => $currentExpiry->toDateString(),
                'period_end'    => $newExpiry->toDateString(),
                'move_id'       => $moveData['move']->id ?? null,
                'move_line_id'  => $moveData['line']->id ?? null,
            ]);

            // Notify remote system
            app(OnlineSystemProvisioningService::class)->renewInstance($instance);

            return true;
        });
    }

    /**
     * Create Account Move Invoice linked to the plan's service product
     */
    protected function createSubscriptionInvoice(
        OnlineInstance $instance,
        OnlineSystemPlan $plan,
        Partner $partner,
        float $price,
        BillingCycle $cycle,
        string $context,
        ?string $referralCode = null,
    ): array {
        if (! DatabaseSchema::hasTable('accounts_account_moves') || ! DatabaseSchema::hasTable('accounts_account_move_lines')) {
            throw new Exception('Accounts tables are required for online-system billing.');
        }

        try {
            $companyId = (int) ($partner->company_id ?: current_company_id());
            $company = Company::query()->find($companyId);
            if (! $company || ! $company->currency_id) {
                throw new Exception('A company with a currency is required for online-system billing.');
            }

            $journal = Journal::where('type', JournalType::SALE)
                ->where('company_id', $company->id)
                ->first();

            if (! $journal) {
                throw new Exception('A sales journal is required for online-system billing.');
            }

            $userId = Auth::guard('web')->id();

            $accountMove = AccountMove::create([
                'move_type'        => MoveType::OUT_INVOICE->value ?? 'out_invoice',
                'state'            => MoveState::DRAFT->value ?? 'draft',
                'journal_id'       => $journal->id,
                'invoice_origin'   => 'SITE-#'.($instance->instance_number ?? $instance->id),
                'date'             => now()->toDateString(),
                'invoice_date'     => now()->toDateString(),
                'invoice_date_due' => now()->toDateString(),
                'company_id'       => $company->id,
                'currency_id'      => $company->currency_id,
                'partner_id'       => $partner->id,
                'creator_id'       => $userId,
                'invoice_user_id'  => $userId,
            ]);

            $product = $plan->product;
            if (! $product) {
                throw new Exception('The online plan must be linked to a product before billing.');
            }
            $itemName = $product ? $product->name : ($plan->name.' — '.$plan->system?->name);

            $line = $accountMove->invoiceLines()->create([
                'name'         => $itemName.' ('.$cycle->getLabel().') - '.$instance->name,
                'date'         => $accountMove->date,
                'display_type' => DisplayType::PRODUCT->value ?? 'product',
                'parent_state' => MoveState::DRAFT->value ?? 'draft',
                'quantity'     => 1,
                'price_unit'   => $price,
                'currency_id'  => $accountMove->currency_id,
                'product_id'   => $product?->id,
                'uom_id'       => $product?->uom_id,
                'creator_id'   => $userId,
            ]);

            if (
                filled($referralCode)
                && class_exists(ReferralService::class)
                && Package::isPluginInstalled('referrals')
            ) {
                app(ReferralService::class)->applyToDraftMove(
                    $accountMove,
                    $referralCode,
                    ReferralService::CONTEXT_ONLINE,
                    OnlineInstance::class,
                    $instance->id,
                );
            }

            AccountFacade::computeAccountMove($accountMove->refresh());
            $accountMove->refresh();

            if (! $this->hasSufficientBalanceLocked($partner, (float) $accountMove->amount_total, $company->id, $company->currency_id)) {
                throw ValidationException::withMessages([
                    'referralCode' => __('software-online::filament/customer/pages/explore.insufficient_balance'),
                ]);
            }

            $accountMove = app(MoveWorkflow::class)->post($accountMove);
            $this->reconcileWithCustomerCredit($accountMove, $partner);
            $accountMove->refresh();
            $accountMove->computePaymentState();
            $accountMove->save();

            if ($accountMove->payment_state !== PaymentState::PAID) {
                throw new Exception('Online subscription invoice could not be fully settled from customer credit.');
            }

            MovePaid::dispatch($accountMove);

            return [
                'move' => $accountMove,
                'line' => $line,
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to create subscription invoice: '.$e->getMessage());

            throw $e;
        }
    }

    private function hasSufficientBalanceLocked(Partner $partner, float $amount, int $companyId, int $currencyId): bool
    {
        $available = MoveLine::query()
            ->where('partner_id', $partner->id)
            ->where('company_id', $companyId)
            ->where('currency_id', $currencyId)
            ->where('parent_state', MoveState::POSTED)
            ->where('reconciled', false)
            ->where('amount_residual', '<', 0)
            ->whereHas('account', fn ($query) => $query->where('account_type', AccountType::ASSET_RECEIVABLE))
            ->lockForUpdate()
            ->get()
            ->sum(fn (MoveLine $line): float => abs((float) $line->amount_residual));

        return $available + 0.0001 >= $amount;
    }

    /**
     * Reconcile a posted customer invoice against the partner's open credit
     * entries (negative residual receivable lines), deducting the balance.
     */
    protected function reconcileWithCustomerCredit(AccountMove $move, Partner $partner): void
    {
        $invoiceLine = $move->lines()
            ->where('balance', '>', 0)
            ->where('company_id', $move->company_id)
            ->where('currency_id', $move->currency_id)
            ->whereHas('account', fn ($q) => $q->where('account_type', AccountType::ASSET_RECEIVABLE))
            ->lockForUpdate()
            ->first();

        if (! $invoiceLine) {
            return;
        }

        $creditLines = MoveLine::query()
            ->where('partner_id', $partner->id)
            ->where('company_id', $move->company_id)
            ->where('currency_id', $move->currency_id)
            ->where('account_id', $invoiceLine->account_id)
            ->where('parent_state', MoveState::POSTED)
            ->where('reconciled', false)
            ->where('balance', '<', 0)
            ->where('amount_residual', '<', 0)
            ->whereHas('account', fn ($query) => $query->where('account_type', AccountType::ASSET_RECEIVABLE))
            ->orderBy('date')
            ->lockForUpdate()
            ->get();

        app(Reconciler::class)->reconcile(
            $creditLines->push($invoiceLine)
        );
    }
}
