<?php

namespace Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource;

class ListPaymentRequests extends ListRecords
{
    protected static string $resource = PaymentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('accounts::filament/customer/payment-request.actions.create'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
