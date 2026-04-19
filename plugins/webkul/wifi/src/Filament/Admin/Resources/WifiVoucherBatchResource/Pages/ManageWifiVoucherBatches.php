<?php

namespace Webkul\Wifi\Filament\Admin\Resources\WifiVoucherBatchResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Wifi\Filament\Admin\Resources\WifiVoucherBatchResource;

class ManageWifiVoucherBatches extends ManageRecords
{
    protected static string $resource = WifiVoucherBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Batch')->icon('heroicon-o-plus-circle'),
        ];
    }
}
