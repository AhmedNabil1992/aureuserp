<?php

namespace Webkul\Wifi\Filament\Admin\Resources\WifiPartnerCloudResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Wifi\Filament\Admin\Resources\WifiPartnerCloudResource;

class ManageWifiPartnerClouds extends ManageRecords
{
    protected static string $resource = WifiPartnerCloudResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('wifi::filament/resources/wifi_partner_cloud.form.buttons.new-mapping'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
