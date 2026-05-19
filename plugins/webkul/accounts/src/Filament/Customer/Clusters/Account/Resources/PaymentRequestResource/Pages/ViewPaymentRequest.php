<?php

namespace Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource;
use Webkul\Account\Models\Payment;

class ViewPaymentRequest extends ViewRecord
{
    protected static string $resource = PaymentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->label(__('accounts::filament/customer/payment-request.actions.cancel'))
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->visible(fn (Payment $record): bool => $record->state === PaymentStatus::DRAFT)
                ->action(function (Payment $record): void {
                    $record->state = PaymentStatus::CANCELED;
                    $record->save();

                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/customer/payment-request.notifications.canceled.title'))
                        ->body(__('accounts::filament/customer/payment-request.notifications.canceled.body'))
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
