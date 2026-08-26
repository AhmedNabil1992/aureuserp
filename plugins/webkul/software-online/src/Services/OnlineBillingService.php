<?php

namespace Webkul\SoftwareOnline\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\MoveLine;
use Webkul\Partner\Models\Partner;
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
        $balance = (float) MoveLine::query()
            ->where('partner_id', $partner->id)
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
    public function hasSufficientBalance(Partner $partner, float $amount): bool
    {
        return $this->getAvailableBalance($partner) >= $amount;
    }

    /**
     * Process new website subscription from customer balance
     */
    public function subscribeNewInstance(
        Partner $partner,
        OnlineSystemPlan $plan,
        string $name,
        ?string $subdomain,
        BillingCycle $cycle,
        ?string $adminEmail = null,
        ?string $adminUsername = null
    ): OnlineInstance {
        $price = $cycle === BillingCycle::Annual ? $plan->annual_price : $plan->monthly_price;

        if ($price > 0 && ! $this->hasSufficientBalance($partner, (float) $price)) {
            throw new Exception(__('software-online::filament/customer/pages/explore.insufficient_balance'));
        }

        return DB::transaction(function () use ($partner, $plan, $name, $subdomain, $cycle, $price, $adminEmail, $adminUsername) {
            $startsAt = now();
            $expiresAt = $cycle === BillingCycle::Annual ? now()->addYear() : now()->addMonth();

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

            // Generate invoice move if accounts module is active
            $moveData = $this->createSubscriptionInvoice($instance, $plan, $partner, $price, $cycle, 'new_subscription');

            if ($moveData) {
                $instance->update(['move_id' => $moveData['move']->id]);
            }

            // Record transaction
            OnlineInstanceTransaction::create([
                'instance_id'   => $instance->id,
                'partner_id'    => $partner->id,
                'type'          => TransactionType::NewSubscription,
                'billing_cycle' => $cycle,
                'amount'        => $price,
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
        $cycle = $cycle ?? $instance->billing_cycle ?? BillingCycle::Monthly;

        $price = $cycle === BillingCycle::Annual ? $plan->annual_price : $plan->monthly_price;

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
        string $context
    ): ?array {
        if (! DatabaseSchema::hasTable('accounts_account_moves') || ! DatabaseSchema::hasTable('accounts_account_move_lines')) {
            return null;
        }

        try {
            $company = $partner->company ?? Company::first();
            if (! $company || ! $company->currency_id) {
                return null;
            }

            $journal = Journal::where('type', JournalType::SALE->value ?? 'sale')
                ->where('company_id', $company->id)
                ->first() ?? Journal::first();

            if (! $journal) {
                return null;
            }

            $accountMove = AccountMove::create([
                'move_type'        => MoveType::OUT_INVOICE->value ?? 'out_invoice',
                'state'            => MoveState::DRAFT->value ?? 'draft',
                'journal_id'       => $journal->id,
                'invoice_origin'   => 'SITE-#' . $instance->instance_number,
                'date'             => now()->toDateString(),
                'invoice_date'     => now()->toDateString(),
                'invoice_date_due' => now()->toDateString(),
                'company_id'       => $company->id,
                'currency_id'      => $company->currency_id,
                'partner_id'       => $partner->id,
                'creator_id'       => Auth::id() ?? $partner->id,
                'invoice_user_id'  => Auth::id() ?? $partner->id,
            ]);

            $product = $plan->product;
            $itemName = $product ? $product->name : ($plan->name . ' — ' . $plan->system?->name);

            $line = $accountMove->invoiceLines()->create([
                'name'         => $itemName . ' (' . $cycle->getLabel() . ') - ' . $instance->name,
                'date'         => $accountMove->date,
                'display_type' => DisplayType::PRODUCT->value ?? 'product',
                'parent_state' => MoveState::DRAFT->value ?? 'draft',
                'quantity'     => 1,
                'price_unit'   => $price,
                'currency_id'  => $accountMove->currency_id,
                'product_id'   => $product?->id,
                'uom_id'       => $product?->uom_id,
                'creator_id'   => Auth::id() ?? $partner->id,
            ]);

            try {
                if (class_exists(AccountFacade::class)) {
                    AccountFacade::computeAccountMove($accountMove);
                }
            } catch (\Throwable $e) {
                // Calculation fallback
            }

            // Post the invoice so it appears in ERP and affects the customer balance
            try {
                $accountMove = app(\Webkul\Account\Services\MoveWorkflow::class)->post($accountMove);
            } catch (\Throwable $e) {
                Log::warning('Failed to post subscription invoice: ' . $e->getMessage());
            }

            // Auto-reconcile the posted invoice against the customer's available credit
            try {
                $this->reconcileWithCustomerCredit($accountMove, $partner);
            } catch (\Throwable $e) {
                Log::warning('Failed to reconcile subscription invoice with customer credit: ' . $e->getMessage());
            }

            return [
                'move' => $accountMove,
                'line' => $line,
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to create subscription invoice: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Reconcile a posted customer invoice against the partner's open credit
     * entries (negative residual receivable lines), deducting the balance.
     */
    protected function reconcileWithCustomerCredit(AccountMove $move, Partner $partner): void
    {
        $invoiceLine = $move->lines()
            ->where('balance', '>', 0)
            ->whereHas('account', fn ($q) => $q->where('account_type', AccountType::ASSET_RECEIVABLE))
            ->first();

        if (! $invoiceLine) {
            return;
        }

        $creditLines = MoveLine::query()
            ->where('partner_id', $partner->id)
            ->where('parent_state', MoveState::POSTED)
            ->where('reconciled', false)
            ->where('balance', '<', 0)
            ->where('amount_residual', '<', 0)
            ->whereHas('account', fn ($query) => $query->where('account_type', AccountType::ASSET_RECEIVABLE))
            ->orderBy('date')
            ->get();

        $remaining = (float) $invoiceLine->amount_residual;

        foreach ($creditLines as $creditLine) {
            if ($remaining <= 0.001) {
                break;
            }

            $available = abs((float) $creditLine->amount_residual);

            if ($available <= 0.001) {
                continue;
            }

            $partial = min($available, $remaining);

            $creditLine->matchedDebits()->create([
                'company_id'          => $move->company_id,
                'credit_move_line_id' => $creditLine->id,
                'debit_move_line_id'  => $invoiceLine->id,
                'debit_currency_id'   => $invoiceLine->currency_id,
                'credit_currency_id'  => $creditLine->currency_id,
                'debit_amount_currency'  => $partial,
                'credit_amount_currency' => $partial,
                'creator_id'          => Auth::id(),
                'max_date'            => now()->toDateString(),
                'amount'              => $partial,
            ]);

            $remaining -= $partial;
        }

        app(\Webkul\Account\Services\Reconciler::class)->refreshMatchingNumbers(
            $creditLines->pluck('id')->push($invoiceLine->id)->unique()->all()
        );
    }
}
