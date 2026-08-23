<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlinePlanResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlinePlanResource;

class ListOnlinePlans extends ListRecords
{
    protected static string $resource = OnlinePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
