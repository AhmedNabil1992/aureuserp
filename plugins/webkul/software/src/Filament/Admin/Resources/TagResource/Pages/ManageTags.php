<?php

namespace Webkul\Software\Filament\Admin\Resources\TagResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\TagResource;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Tag')->icon('heroicon-o-plus-circle'),
        ];
    }
}
