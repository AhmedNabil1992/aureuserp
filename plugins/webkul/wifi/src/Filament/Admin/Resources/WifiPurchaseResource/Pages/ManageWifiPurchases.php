<?php

namespace Webkul\Wifi\Filament\Admin\Resources\WifiPurchaseResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Wifi\Filament\Admin\Resources\WifiPurchaseResource;

class ManageWifiPurchases extends ManageRecords
{
    protected static string $resource = WifiPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Purchase')->icon('heroicon-o-plus-circle'),
        ];
    }
}
