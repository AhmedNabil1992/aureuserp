<?php

namespace Webkul\Wifi\Filament\Admin\Resources\WifiPackageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Wifi\Filament\Admin\Resources\WifiPackageResource;

class ManageWifiPackages extends ManageRecords
{
    protected static string $resource = WifiPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('wifi::filament/resources/wifi_package.form.buttons.new-package'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
