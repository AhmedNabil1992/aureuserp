<?php

declare(strict_types=1);

namespace Webkul\Software\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;
use Webkul\Referral\Services\ReferralWalletService;

class CustomerInvoiceSettlementService
{
    public function assertSufficientBalance(Partner $partner, Move $invoice): void
    {
        $credit = MoveLine::query()
            ->where('partner_id', $partner->id)
            ->where('company_id', $invoice->company_id)
            ->where('currency_id', $invoice->currency_id)
            ->where('parent_state', MoveState::POSTED)
            ->where('reconciled', false)
            ->where('amount_residual', '<', 0)
            ->whereHas('account', fn ($query) => $query->where('account_type', AccountType::ASSET_RECEIVABLE))
            ->lockForUpdate()
            ->get()
            ->sum(fn (MoveLine $line): float => abs((float) $line->amount_residual));

        $wallet = $this->referralBalance($partner, $invoice);

        if ($credit + $wallet + 0.0001 < (float) $invoice->amount_total) {
            throw ValidationException::withMessages([
                'service_type' => 'Your available balance is not enough to pay this invoice.',
            ]);
        }
    }

    public function postAndSettle(Move $invoice, Partner $partner): Move
    {
        $this->assertSufficientBalance($partner, $invoice);
        $invoice = AccountFacade::confirmMove($invoice->refresh());

        if ($this->referralsAvailable()) {
            app(ReferralWalletService::class)->applyToPostedInvoice($invoice);
        }

        $invoice->loadMissing(['paymentTermLines.account']);

        foreach ($invoice->paymentTermLines->where('reconciled', false) as $termLine) {
            $credits = MoveLine::query()
                ->where('partner_id', $partner->id)
                ->where('company_id', $invoice->company_id)
                ->where('currency_id', $invoice->currency_id)
                ->where('account_id', $termLine->account_id)
                ->where('parent_state', MoveState::POSTED)
                ->where('reconciled', false)
                ->where('amount_residual', '<', 0)
                ->where('move_id', '!=', $invoice->id)
                ->orderBy('date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($credits->isNotEmpty()) {
                AccountFacade::reconcile((new EloquentCollection([$termLine]))->merge($credits));
            }
        }

        $invoice->refresh();
        $invoice->computePaymentState();
        $invoice->save();

        if (abs((float) $invoice->amount_residual) > 0.0001 || $invoice->payment_state !== PaymentState::PAID) {
            throw new RuntimeException('The invoice could not be fully paid from the customer balance.');
        }

        return $invoice;
    }

    private function referralBalance(Partner $partner, Move $invoice): float
    {
        if (! $this->referralsAvailable()) {
            return 0.0;
        }

        $wallet = app(ReferralWalletService::class);

        return min(
            $wallet->balance($invoice->company_id, $invoice->currency_id, $partner->id),
            $wallet->maximumUsableForInvoice($invoice),
        );
    }

    private function referralsAvailable(): bool
    {
        return class_exists(ReferralWalletService::class)
            && Package::isPluginInstalled('referrals')
            && Schema::hasTable('referral_wallet_balances');
    }
}
