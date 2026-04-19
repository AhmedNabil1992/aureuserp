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
            CreateAction::make()->label('New Package')->icon('heroicon-o-plus-circle'),
        ];
    }
}
