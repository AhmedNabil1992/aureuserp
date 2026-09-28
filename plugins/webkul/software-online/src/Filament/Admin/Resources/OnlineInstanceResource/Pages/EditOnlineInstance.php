<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource;
use Webkul\SoftwareOnline\Services\OnlineSystemProvisioningService;

class EditOnlineInstance extends EditRecord
{
    protected static string $resource = OnlineInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            OnlineInstanceResource::deleteRemoteAction(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->wasChanged('plan_id') && filled($this->record->remote_tenant_id)) {
            $success = app(OnlineSystemProvisioningService::class)->updateEntitlements($this->record);

            if (! $success) {
                $this->record->refresh();

                Notification::make()
                    ->title(__('software-online::filament/admin/resources/instance.notifications.entitlements_failed'))
                    ->body($this->record->last_api_error)
                    ->warning()
                    ->send();
            }
        }
    }
}
