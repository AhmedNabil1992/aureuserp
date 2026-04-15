<?php

namespace Webkul\Software\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Software\Enums\LicensePlan;
use Webkul\Software\Enums\LicenseStatus;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseInvoice;
use Webkul\Software\Models\ProgramEdition;

class LicenseManager
{
    public function __construct(
        protected LicenseInvoiceManager $invoiceManager,
        protected SubscriptionManager $subscriptionManager,
    ) {}

    /**
     * Bill a license with specific edition and plan
     *
     * @return array{license: License, invoice: ?LicenseInvoice, invoiceNumber: ?string, isTrial: bool}
     */
    public function billLicense(
        License $license,
        int $editionId,
        string $licensePlan
    ): array {
        return DB::transaction(function () use ($license, $editionId, $licensePlan) {
            // Validate
            $this->validateBillingRequest($license, $editionId, $licensePlan);

            // Update License
            $updatedLicense = $this->updateLicenseForBilling(
                $license,
                $editionId,
                $licensePlan
            );

            $plan = LicensePlan::from($licensePlan);

            if ($plan === LicensePlan::Trial) {
                return [
                    'license'       => $updatedLicense,
                    'invoice'       => null,
                    'invoiceNumber' => null,
                    'isTrial'       => true,
                ];
            }

            // Create Invoice
            $invoiceResult = $this->invoiceManager->createInvoice(
                $updatedLicense,
                $editionId,
                $licensePlan
            );

            // Create Subscriptions
            $this->subscriptionManager->createForLicense(
                $updatedLicense,
                $editionId
            );

            return [
                'license'       => $updatedLicense,
                'invoice'       => $invoiceResult['invoice'],
                'invoiceNumber' => $invoiceResult['invoice']->invoice_number,
                'isTrial'       => false,
            ];
        });
    }

    /**
     * Renew an existing license
     *
     * @return array{license: License, invoice: LicenseInvoice}
     */
    public function renewLicense(
        License $license,
        string $plan = 'annual'
    ): array {
        return DB::transaction(function () use ($license, $plan) {
            $this->validateRenewalRequest($license);

            $updatedLicense = $this->renewLicenseDateAndStatus($license, $plan);

            $invoiceResult = $this->invoiceManager->createInvoice(
                $updatedLicense,
                $updatedLicense->edition_id,
                $plan
            );

            $this->subscriptionManager->renewForLicense($updatedLicense);

            return [
                'license' => $updatedLicense,
                'invoice' => $invoiceResult['invoice'],
            ];
        });
    }

    /**
     * Activate a license
     */
    public function activateLicense(License $license): License
    {
        $license->update([
            'is_active' => true,
            'status'    => LicenseStatus::Approved,
        ]);

        $this->subscriptionManager->reactivateForLicense($license);

        return $license->refresh();
    }

    /**
     * Deactivate a license
     */
    public function deactivateLicense(License $license): License
    {
        $license->update([
            'is_active' => false,
        ]);

        $this->subscriptionManager->suspendForLicense($license);

        return $license->refresh();
    }

    /**
     * Expire a license
     */
    public function expireLicense(License $license): License
    {
        $license->update([
            'is_active' => false,
            'end_date'  => now()->toDateString(),
        ]);

        $this->subscriptionManager->expireForLicense($license);

        return $license->refresh();
    }

    /**
     * Validate billing request
     *
     * @throws \RuntimeException
     */
    private function validateBillingRequest(
        License $license,
        int $editionId,
        string $licensePlan
    ): void {
        if ($license->invoices()->exists()) {
            throw new \RuntimeException('License already has invoices.');
        }

        if ($license->devices()->whereNotNull('license_key')->exists()) {
            throw new \RuntimeException('Cannot bill license with active devices.');
        }

        try {
            LicensePlan::from($licensePlan);
        } catch (\ValueError $exception) {
            throw new \RuntimeException('Invalid license plan: '.$licensePlan);
        }

        $plan = LicensePlan::from($licensePlan);

        $edition = ProgramEdition::find($editionId);
        if (! $edition) {
            throw new \RuntimeException('Edition not found.');
        }

        if ($edition->program_id !== $license->program_id) {
            throw new \RuntimeException('Edition does not belong to the selected program.');
        }
    }

    /**
     * Update license for billing
     */
    private function updateLicenseForBilling(
        License $license,
        int $editionId,
        string $licensePlan
    ): License {
        $plan = LicensePlan::from($licensePlan);

        $license->update([
            'edition_id'   => $editionId,
            'license_plan' => $plan,
            'period'       => $this->computePeriod($plan),
            'start_date'   => now()->toDateString(),
            'end_date'     => $this->computeEndDate($plan),
            'status'       => LicenseStatus::Approved,
            'is_active'    => true,
            'approved_by'  => Auth::id(),
        ]);

        return $license->refresh();
    }

    /**
     * Compute period based on license plan
     */
    private function computePeriod(LicensePlan $plan): int
    {
        return match ($plan) {
            LicensePlan::Trial   => 7,
            LicensePlan::Full    => 0,
            LicensePlan::Monthly => 30,
            LicensePlan::Annual  => 365,
        };
    }

    /**
     * Compute end date based on license plan
     */
    private function computeEndDate(LicensePlan $plan): ?string
    {
        return match ($plan) {
            LicensePlan::Trial   => now()->addDays(7)->toDateString(),
            LicensePlan::Full    => null,
            LicensePlan::Monthly => now()->addMonth()->toDateString(),
            LicensePlan::Annual  => now()->addYear()->toDateString(),
        };
    }

    /**
     * Validate renewal request
     *
     * @throws \RuntimeException
     */
    private function validateRenewalRequest(License $license): void
    {
        if (! $license->is_active) {
            throw new \RuntimeException('License is not active.');
        }

        if ($license->license_plan === null) {
            throw new \RuntimeException('License has not been billed yet.');
        }
    }

    /**
     * Renew license date and status
     */
    private function renewLicenseDateAndStatus(
        License $license,
        string $plan
    ): License {
        $planEnum = LicensePlan::from($plan);

        $license->update([
            'license_plan' => $planEnum,
            'period'       => $this->computePeriod($planEnum),
            'start_date'   => now()->toDateString(),
            'end_date'     => $this->computeEndDate($planEnum),
            'is_active'    => true,
            'status'       => LicenseStatus::Approved,
        ]);

        return $license->refresh();
    }
}
