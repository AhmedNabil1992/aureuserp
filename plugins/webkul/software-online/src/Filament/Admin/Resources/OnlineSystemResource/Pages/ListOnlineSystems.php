<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineSystemResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineSystemResource;

class ListOnlineSystems extends ListRecords
{
    protected static string $resource = OnlineSystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
