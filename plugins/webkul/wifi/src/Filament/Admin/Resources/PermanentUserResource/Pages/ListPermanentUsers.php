<?php

namespace Webkul\Wifi\Filament\Admin\Resources\PermanentUserResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Wifi\Filament\Admin\Resources\PermanentUserResource;

class ListPermanentUsers extends ListRecords
{
    protected static string $resource = PermanentUserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
