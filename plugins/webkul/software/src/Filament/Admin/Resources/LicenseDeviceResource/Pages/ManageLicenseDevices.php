<?php

namespace Webkul\Software\Filament\Admin\Resources\LicenseDeviceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\LicenseDeviceResource;

class ManageLicenseDevices extends ManageRecords
{
    protected static string $resource = LicenseDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Device')->icon('heroicon-o-plus-circle'),
        ];
    }
}
