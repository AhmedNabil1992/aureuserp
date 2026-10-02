<?php

declare(strict_types=1);

namespace Webkul\Software\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\MoveLine;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Enums\ProductType;
use Webkul\Software\Enums\LicensePlan;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseInvoice;
use Webkul\Software\Models\LicenseSubscription;
use Webkul\Software\Models\ProgramFeature;
use Webkul\Support\Models\Company;

class LicenseSubscriptionBillingService
{
    public function __construct(protected CustomerInvoiceSettlementService $settlementService) {}

    /** @return array{subscription: LicenseSubscription, invoice: Move} */
    public function subscribeOrRenew(License $license, int $featureId, bool $payFromCustomerBalance): array
    {
        return DB::transaction(function () use ($license, $featureId, $payFromCustomerBalance): array {
            $license = License::query()->with('partner')->lockForUpdate()->findOrFail($license->id);
            $feature = ProgramFeature::query()->with('product')->findOrFail($featureId);

            if ($feature->program_id !== $license->program_id || ! $feature->service_type) {
                throw new RuntimeException('The selected service does not belong to this license program.');
            }

            $product = $feature->product;
            $productType = $product?->type?->value ?? $product?->type;
            $price = (float) ($product?->price ?? $feature->amount ?? 0);

            if (! $product || $productType !== ProductType::SERVICE->value || $price <= 0) {
                throw new RuntimeException('The selected service must have a billable service product and price.');
            }

            $partner = $license->partner;
            if (! $partner) {
                throw new RuntimeException('The license has no customer.');
            }

            if ($payFromCustomerBalance && (int) Auth::guard('customer')->id() !== (int) $partner->id) {
                throw new RuntimeException('You cannot renew services for another customer.');
            }

            $company = $this->resolveBillingCompany($partner);
            if (! $company?->currency_id) {
                throw new RuntimeException('A company with a currency is required.');
            }

            $journal = Journal::query()
                ->where('company_id', $company->id)
                ->where('type', JournalType::SALE)
                ->firstOrFail();

            $move = Move::query()->create([
                'move_type'        => MoveType::OUT_INVOICE,
                'state'            => MoveState::DRAFT,
                'journal_id'       => $journal->id,
                'invoice_origin'   => $license->serial_number,
                'date'             => now()->toDateString(),
                'invoice_date'     => now()->toDateString(),
                'invoice_date_due' => now()->toDateString(),
                'company_id'       => $company->id,
                'currency_id'      => $company->currency_id,
                'partner_id'       => $partner->id,
                'creator_id'       => Auth::guard('web')->id(),
                'invoice_user_id'  => Auth::guard('web')->id(),
            ]);

            $move->invoiceLines()->create([
                'name'         => $feature->name.' (1 year)',
                'date'         => $move->date,
                'display_type' => DisplayType::PRODUCT,
                'parent_state' => MoveState::DRAFT,
                'quantity'     => 1,
                'price_unit'   => $price,
                'currency_id'  => $move->currency_id,
                'product_id'   => $product->id,
                'uom_id'       => $product->uom_id,
                'creator_id'   => Auth::guard('web')->id(),
            ]);

            AccountFacade::computeAccountMove($move->refresh());
            $move->refresh();

            if ($payFromCustomerBalance) {
                $move = $this->settlementService->postAndSettle($move, $partner);
            }

            $serviceType = $feature->service_type->value;
            $subscription = LicenseSubscription::query()
                ->where('license_id', $license->id)
                ->where('service_type', $serviceType)
                ->lockForUpdate()
                ->first() ?? new LicenseSubscription(['license_id' => $license->id]);

            $baseDate = $subscription->end_date?->isFuture()
                ? $subscription->end_date->copy()
                : now();

            $subscription->fill([
                'feature_id'   => $feature->id,
                'service_type' => $serviceType,
                'start_date'   => $subscription->start_date ?? now()->toDateString(),
                'end_date'     => $baseDate->addYear()->toDateString(),
                'is_active'    => true,
            ])->save();

            LicenseInvoice::query()->create([
                'license_id'      => $license->id,
                'program_id'      => $license->program_id,
                'edition_id'      => $license->edition_id,
                'license_plan'    => LicensePlan::Annual->value,
                'invoice_number'  => $move->name ?: 'MOVE-'.$move->id,
                'item_name'       => $feature->name,
                'quantity'        => 1,
                'unit_price'      => $price,
                'amount'          => $price,
                'billed_by'       => Auth::guard('web')->id(),
                'billed_at'       => now(),
                'notes'           => 'One-year service subscription',
                'account_move_id' => $move->id,
            ]);

            return ['subscription' => $subscription->refresh(), 'invoice' => $move->refresh()];
        });
    }

    private function resolveBillingCompany(Partner $partner): ?Company
    {
        $companyIds = collect([(int) $partner->company_id]);

        $companyIds->push(...MoveLine::query()
            ->where('partner_id', $partner->id)
            ->where('parent_state', MoveState::POSTED)
            ->where('reconciled', false)
            ->where('amount_residual', '<', 0)
            ->whereHas('account', fn ($query) => $query->where('account_type', 'asset_receivable'))
            ->orderBy('date')
            ->pluck('company_id')
            ->map(fn ($companyId): int => (int) $companyId)
            ->all());

        if (Schema::hasTable('referral_wallet_balances')) {
            $companyIds->push(...DB::table('referral_wallet_balances')
                ->where('partner_id', $partner->id)
                ->where('available_amount', '>', 0)
                ->pluck('company_id')
                ->map(fn ($companyId): int => (int) $companyId)
                ->all());
        }

        $companyIds->push((int) current_company_id());

        $companyIds->push(...Journal::query()
            ->where('type', JournalType::SALE)
            ->orderBy('id')
            ->pluck('company_id')
            ->map(fn ($companyId): int => (int) $companyId)
            ->all());

        foreach ($companyIds->filter()->unique() as $companyId) {
            $company = Company::query()->find($companyId);

            if ($company?->currency_id && Journal::query()
                ->where('company_id', $company->id)
                ->where('type', JournalType::SALE)
                ->exists()) {
                return $company;
            }
        }

        return null;
    }
}
