<?php

namespace Webkul\Account\Filament\Customer\Resources\BalanceHistoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Customer\Resources\BalanceHistoryResource;
use Webkul\Account\Models\BalanceRequest;

class ListBalanceRequests extends ListRecords
{
    protected static string $resource = BalanceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        $canCreateRequest = ! BalanceRequest::hasPendingRequest(Auth::guard('customer')->id());

        return [
            Actions\CreateAction::make()
                ->label(__('طلب رصيد جديد'))
                ->visible($canCreateRequest)
                ->disabled(! $canCreateRequest),
        ];
    }
}
