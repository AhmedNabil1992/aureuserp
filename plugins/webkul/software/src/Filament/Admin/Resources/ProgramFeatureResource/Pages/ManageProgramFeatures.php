<?php

namespace Webkul\Software\Filament\Admin\Resources\ProgramFeatureResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\ProgramFeatureResource;

class ManageProgramFeatures extends ManageRecords
{
    protected static string $resource = ProgramFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Feature')->icon('heroicon-o-plus-circle'),
        ];
    }
}
