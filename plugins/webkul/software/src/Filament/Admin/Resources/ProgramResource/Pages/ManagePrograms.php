<?php

namespace Webkul\Software\Filament\Admin\Resources\ProgramResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\ProgramResource;

class ManagePrograms extends ManageRecords
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Program')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
