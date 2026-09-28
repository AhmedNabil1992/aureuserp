<?php

namespace Webkul\SoftwareOnline\Filament\Customer\Resources\OnlineInstanceResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Filament\Customer\Resources\OnlineInstanceResource;
use Webkul\SoftwareOnline\Services\OnlineBillingService;

class ViewOnlineInstance extends ViewRecord
{
    protected static string $resource = OnlineInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('visit')
                ->label(__('software-online::filament/customer/resources/my_instances.actions.visit'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('success')
                ->url(fn () => $this->record->full_url)
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->status === InstanceStatus::Active && $this->record->full_url !== '#'),
            Action::make('renew')
                ->label(__('software-online::filament/customer/resources/my_instances.actions.renew'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Select::make('billing_cycle')
                        ->label(__('software-online::filament/customer/resources/my_instances.fields.billing_cycle'))
                        ->options([
                            BillingCycle::Monthly->value => BillingCycle::Monthly->getLabel(),
                            BillingCycle::Annual->value  => BillingCycle::Annual->getLabel(),
                        ])
                        ->default(fn (): string => $this->record->billing_cycle === BillingCycle::Annual
                            ? BillingCycle::Annual->value
                            : BillingCycle::Monthly->value)
                        ->required(),
                    TextInput::make('periods')
                        ->label(__('software-online::filament/customer/resources/my_instances.fields.periods'))
                        ->integer()
                        ->minValue(1)
                        ->maxValue(120)
                        ->default(1)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $cycle = BillingCycle::tryFrom($data['billing_cycle']) ?? BillingCycle::Monthly;
                    try {
                        app(OnlineBillingService::class)->renewInstance(
                            instance: $this->record,
                            cycle: $cycle,
                            periods: (int) ($data['periods'] ?? 1),
                        );
                        Notification::make()
                            ->title(__('software-online::filament/customer/resources/my_instances.notifications.renewed_success'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('software-online::filament/customer/resources/my_instances.notifications.renew_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
