<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource;

class ListOnlineInstances extends ListRecords
{
    protected static string $resource = OnlineInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
