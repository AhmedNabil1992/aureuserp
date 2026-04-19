<?php

namespace Webkul\Wifi\Filament\Admin\Resources\DynamicClientResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Webkul\Wifi\Filament\Admin\Resources\DynamicClientResource;

class ViewDynamicClient extends ViewRecord
{
    protected static string $resource = DynamicClientResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
