<?php

namespace Webkul\Software\Filament\Admin\Resources\ProgramReleaseResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\ProgramReleaseResource;

class ManageProgramReleases extends ManageRecords
{
    protected static string $resource = ProgramReleaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Release')->icon('heroicon-o-plus-circle'),
        ];
    }
}
