<?php

namespace Webkul\Software\Services;

use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseSubscription;
use Webkul\Software\Models\ProgramEdition;
use Webkul\Software\Models\ProgramEditionFeature;
use Webkul\Software\Models\ProgramFeature;

class SubscriptionManager
{
    /**
     * Create subscriptions for a license
     *
     * @return array<int, LicenseSubscription>
     */
    public function createForLicense(
        License $license,
        int $editionId
    ): array {
        $edition = ProgramEdition::query()
            ->with('featureRules.feature')
            ->find($editionId);

        if ($edition?->featureRules->isNotEmpty()) {
            return $this->createConfiguredSubscriptions($license, $edition);
        }

        return $this->createLegacySubscriptions($license);
    }

    /**
     * Renew all subscriptions for a license
     *
     * @return array<int, LicenseSubscription>
     */
    public function renewForLicense(License $license): array
    {
        $edition = $license->edition?->loadMissing('featureRules.feature');

        if ($edition?->featureRules->isNotEmpty()) {
            return $this->renewConfiguredSubscriptions($license, $edition);
        }

        return $this->renewLegacySubscriptions($license);
    }

    /**
     * Expire subscriptions for a license
     */
    public function expireForLicense(License $license): void
    {
        $license->subscriptions()->update([
            'is_active' => false,
            'end_date'  => now()->toDateString(),
        ]);
    }

    /**
     * Suspend subscriptions for a license
     */
    public function suspendForLicense(License $license): void
    {
        $license->subscriptions()->update([
            'is_active' => false,
        ]);
    }

    /**
     * Reactivate subscriptions for a license
     */
    public function reactivateForLicense(License $license): void
    {
        $license->subscriptions()->update([
            'is_active' => true,
        ]);
    }

    /**
     * @return array<int, LicenseSubscription>
     */
    private function createConfiguredSubscriptions(License $license, ProgramEdition $edition): array
    {
        $subscriptions = [];

        foreach ($edition->featureRules->where('auto_attach_on_final_license', true) as $rule) {
            $feature = $rule->feature;

            if (! $feature) {
                throw new \RuntimeException('Edition feature rule is linked to a missing feature.');
            }

            $serviceType = $feature->service_type?->value ?? $feature->service_type;

            if (! $serviceType) {
                throw new \RuntimeException('Feature '.$feature->name.' must have a service type.');
            }

            $subscription = LicenseSubscription::updateOrCreate(
                [
                    'license_id' => $license->id,
                    'feature_id' => $feature->id,
                ],
                [
                    'service_type' => $serviceType,
                    'start_date'   => now()->toDateString(),
                    'end_date'     => $this->resolveConfiguredSubscriptionEndDate($license, $rule, false),
                    'is_active'    => true,
                ]
            );

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }

    /**
     * @return array<int, LicenseSubscription>
     */
    private function createLegacySubscriptions(License $license): array
    {
        $program = $license->program;

        $serviceFeatures = ProgramFeature::query()
            ->where('program_id', $program->id)
            ->whereIn('service_type', [ServiceType::TechnicalSupport->value, ServiceType::Mail->value])
            ->get()
            ->groupBy('service_type');

        $subscriptions = [];
        $subscriptionStart = now()->toDateString();
        $subscriptionEnd = now()->addYear()->toDateString();

        foreach ([ServiceType::TechnicalSupport->value, ServiceType::Mail->value] as $requiredType) {
            $featuresOfType = $serviceFeatures->get($requiredType, collect());

            if ($featuresOfType->count() !== 1) {
                throw new \RuntimeException('Exactly one feature row is required for '.$requiredType.'.');
            }

            $feature = $featuresOfType->first();

            $subscription = LicenseSubscription::updateOrCreate(
                [
                    'license_id' => $license->id,
                    'feature_id' => $feature->id,
                ],
                [
                    'service_type' => $feature->service_type,
                    'start_date'   => $subscriptionStart,
                    'end_date'     => $subscriptionEnd,
                    'is_active'    => true,
                ]
            );

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }

    /**
     * @return array<int, LicenseSubscription>
     */
    private function renewConfiguredSubscriptions(License $license, ProgramEdition $edition): array
    {
        $subscriptions = [];

        foreach ($edition->featureRules->where('auto_renew_with_license', true) as $rule) {
            $feature = $rule->feature;

            if (! $feature) {
                throw new \RuntimeException('Edition feature rule is linked to a missing feature.');
            }

            $serviceType = $feature->service_type?->value ?? $feature->service_type;

            if (! $serviceType) {
                throw new \RuntimeException('Feature '.$feature->name.' must have a service type.');
            }

            $subscriptions[] = LicenseSubscription::updateOrCreate(
                [
                    'license_id' => $license->id,
                    'feature_id' => $feature->id,
                ],
                [
                    'service_type' => $serviceType,
                    'start_date'   => $license->start_date?->toDateString() ?? now()->toDateString(),
                    'end_date'     => $this->resolveConfiguredSubscriptionEndDate($license, $rule, true),
                    'is_active'    => true,
                ]
            );
        }

        return $subscriptions;
    }

    /**
     * @return array<int, LicenseSubscription>
     */
    private function renewLegacySubscriptions(License $license): array
    {
        $subscriptions = $license->subscriptions()->get();

        foreach ($subscriptions as $subscription) {
            $subscription->update([
                'end_date'  => $license->end_date,
                'is_active' => true,
            ]);
        }

        return $subscriptions->fresh()->all();
    }

    private function resolveConfiguredSubscriptionEndDate(
        License $license,
        ProgramEditionFeature $rule,
        bool $isRenewal
    ): ?string {
        if (! $isRenewal && $rule->is_complimentary) {
            return now()->addYear()->toDateString();
        }

        return $license->end_date?->toDateString();
    }
}
