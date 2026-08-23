<?php

namespace Webkul\SoftwareOnline\Filament\Customer\Resources\OnlineInstanceResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\SoftwareOnline\Enums\BillingCycle;
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
                ->visible(fn () => ! empty($this->record->full_url)),
            Action::make('renew')
                ->label(__('software-online::filament/customer/resources/my_instances.actions.renew'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Select::make('billing_cycle')
                        ->label(__('software-online::filament/customer/resources/my_instances.fields.billing_cycle'))
                        ->options(BillingCycle::class)
                        ->default(BillingCycle::Monthly)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $cycle = BillingCycle::tryFrom($data['billing_cycle']) ?? BillingCycle::Monthly;
                    try {
                        app(OnlineBillingService::class)->renewInstance($this->record, $cycle);
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
