<?php

namespace Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\LicenseResource;

class ManageLicenses extends ManageRecords
{
    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New License')->icon('heroicon-o-plus-circle'),
        ];
    }
}
