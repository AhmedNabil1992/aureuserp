<?php

namespace Webkul\Software\Services;

use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseSubscription;
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
     * Renew all subscriptions for a license
     *
     * @return array<int, LicenseSubscription>
     */
    public function renewForLicense(License $license): array
    {
        $subscriptions = $license->subscriptions()->get();

        foreach ($subscriptions as $subscription) {
            $subscription->update([
                'end_date'  => $license->end_date,
                'is_active' => true,
            ]);
        }

        return $subscriptions->fresh()->toArray();
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
}
