<?php

namespace Webkul\Software\Filament\Admin\Resources\RemoteProfileResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\RemoteProfileResource;

class ManageRemoteProfiles extends ManageRecords
{
    protected static string $resource = RemoteProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Remote Profile')->icon('heroicon-o-plus-circle'),
        ];
    }
}
