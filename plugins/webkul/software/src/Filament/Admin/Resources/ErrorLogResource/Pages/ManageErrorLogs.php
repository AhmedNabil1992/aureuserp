<?php

namespace Webkul\Software\Filament\Admin\Resources\ErrorLogResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\ErrorLogResource;

class ManageErrorLogs extends ManageRecords
{
    protected static string $resource = ErrorLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Log')->icon('heroicon-o-plus-circle'),
        ];
    }
}
