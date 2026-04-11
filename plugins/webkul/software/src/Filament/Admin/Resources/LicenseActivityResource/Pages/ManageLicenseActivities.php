<?php

namespace Webkul\Software\Filament\Admin\Resources\LicenseActivityResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\LicenseActivityResource;

class ManageLicenseActivities extends ManageRecords
{
    protected static string $resource = LicenseActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Activity')->icon('heroicon-o-plus-circle'),
        ];
    }
}
