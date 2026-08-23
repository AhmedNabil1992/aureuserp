<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource;

class ViewOnlineInstance extends ViewRecord
{
    protected static string $resource = OnlineInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
