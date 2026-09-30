<?php

declare(strict_types=1);

namespace Webkul\Software\Filament\Admin\Resources\FirmwareResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\FirmwareResource;

class ManageFirmwares extends ManageRecords
{
    protected static string $resource = FirmwareResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
