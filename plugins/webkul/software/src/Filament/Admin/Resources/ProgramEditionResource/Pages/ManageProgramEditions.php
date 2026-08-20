<?php

namespace Webkul\Software\Filament\Admin\Resources\ProgramEditionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\ProgramEditionResource;

class ManageProgramEditions extends ManageRecords
{
    protected static string $resource = ProgramEditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('software::filament/admin/resources/program-edition.title.create'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
