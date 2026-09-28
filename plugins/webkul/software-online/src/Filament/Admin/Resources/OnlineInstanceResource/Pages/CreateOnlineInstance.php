<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource;
use Webkul\SoftwareOnline\Models\OnlineSystemPlan;
use Webkul\SoftwareOnline\Services\OnlineSystemProvisioningService;

class CreateOnlineInstance extends CreateRecord
{
    protected static string $resource = OnlineInstanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $plan = OnlineSystemPlan::query()->with('system')->findOrFail($data['plan_id']);
        $cycleValue = $data['billing_cycle'] instanceof BillingCycle
            ? $data['billing_cycle']->value
            : (string) $data['billing_cycle'];
        $cycle = BillingCycle::tryFrom($cycleValue) ?? BillingCycle::Monthly;
        $startsAt = now();

        app(OnlineSystemProvisioningService::class)->assertDomainAvailable(
            $plan->system,
            (string) ($data['subdomain'] ?? ''),
        );

        $data['system_id'] = $plan->system_id;
        $data['status'] = InstanceStatus::Pending;
        $data['starts_at'] = $startsAt;
        $data['expires_at'] = $plan->expiresAtFor($cycle, $startsAt);
        $data['price'] = $plan->priceFor($cycle);
        $data['instance_url'] = $plan->system?->tenantLoginUrl((string) ($data['subdomain'] ?? ''));

        return $data;
    }

    protected function afterCreate(): void
    {
        app(OnlineSystemProvisioningService::class)->provisionInstance($this->record);
    }
}
