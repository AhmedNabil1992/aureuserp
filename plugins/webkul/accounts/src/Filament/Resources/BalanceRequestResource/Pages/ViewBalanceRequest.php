<?php

namespace Webkul\Account\Filament\Resources\BalanceRequestResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\BalanceRequestResource;

class ViewBalanceRequest extends ViewRecord
{
    protected static string $resource = BalanceRequestResource::class;

    public function getTitle(): string
    {
        return __('تفاصيل طلب الرصيد');
    }
}
