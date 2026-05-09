<?php

namespace Webkul\Account\Filament\Resources\BalanceRequestResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Account\Filament\Resources\BalanceRequestResource;

class ListBalanceRequests extends ListRecords
{
    protected static string $resource = BalanceRequestResource::class;

    public function getTitle(): string
    {
        return __('طلبات الرصيد');
    }
}
