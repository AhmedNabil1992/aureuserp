<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Partner\Models\Partner;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineSystemPlan;
use Webkul\SoftwareOnline\Services\OnlineBillingService;
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

        $data['system_id'] = $plan->system_id;
        $data['status'] = InstanceStatus::Pending;
        $data['starts_at'] = $startsAt;
        $data['expires_at'] = $plan->expiresAtFor($cycle, $startsAt);
        $data['price'] = $plan->priceFor($cycle);
        $data['instance_url'] = $plan->system?->tenantLoginUrl((string) ($data['subdomain'] ?? ''));

        return $data;
    }

    protected function beforeCreate(): void
    {
        $plan = OnlineSystemPlan::query()->with('system')->findOrFail($this->data['plan_id']);

        try {
            app(OnlineSystemProvisioningService::class)->assertDomainAvailable(
                $plan->system,
                strtolower(trim((string) ($this->data['subdomain'] ?? ''))),
            );
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first()
                ?? __('software-online::validation.domain_check_failed');

            $this->addError('data.subdomain', $message);

            Notification::make()
                ->danger()
                ->title(__('software-online::validation.domain_check_title'))
                ->body($message)
                ->send();

            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        $instanceId = $this->record->getKey();

        DB::afterCommit(function () use ($instanceId): void {
            $instance = OnlineInstance::query()
                ->with(['partner', 'plan', 'system'])
                ->find($instanceId);

            if ($instance) {
                app(OnlineSystemProvisioningService::class)->provisionInstance($instance);
            }
        });
    }

    protected function handleRecordCreation(array $data): Model
    {
        $partner = Partner::query()->findOrFail($data['partner_id']);
        $plan = OnlineSystemPlan::query()->with(['system', 'product'])->findOrFail($data['plan_id']);
        $cycleValue = $data['billing_cycle'] instanceof BillingCycle
            ? $data['billing_cycle']->value
            : (string) $data['billing_cycle'];
        $cycle = BillingCycle::tryFrom($cycleValue) ?? BillingCycle::Monthly;

        try {
            return app(OnlineBillingService::class)->createSubscriptionRecord(
                partner: $partner,
                plan: $plan,
                name: (string) $data['name'],
                subdomain: (string) $data['subdomain'],
                cycle: $cycle,
                adminUsername: filled($data['admin_username'] ?? null) ? (string) $data['admin_username'] : null,
                autoRenew: (bool) ($data['auto_renew'] ?? true),
                customDomain: filled($data['custom_domain'] ?? null) ? (string) $data['custom_domain'] : null,
                requireFullSettlement: false,
            );
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first()
                ?? __('software-online::filament/customer/pages/explore.insufficient_balance');

            $this->addError('data.partner_id', $message);

            Notification::make()
                ->danger()
                ->title(__('software-online::filament/customer/pages/explore.notifications.failed'))
                ->body($message)
                ->send();

            $this->halt();

            throw $exception;
        } catch (\Throwable $exception) {
            Notification::make()
                ->danger()
                ->title(__('software-online::filament/customer/pages/explore.notifications.failed'))
                ->body($exception->getMessage())
                ->send();

            $this->halt();

            throw $exception;
        }
    }
}
