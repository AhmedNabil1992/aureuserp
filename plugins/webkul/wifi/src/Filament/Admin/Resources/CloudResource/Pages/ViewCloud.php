<?php

namespace Webkul\Wifi\Filament\Admin\Resources\CloudResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Webkul\Wifi\Filament\Admin\Resources\CloudResource;

class ViewCloud extends ViewRecord
{
    protected static string $resource = CloudResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
