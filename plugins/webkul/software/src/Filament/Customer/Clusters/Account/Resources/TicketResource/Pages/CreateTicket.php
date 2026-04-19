<?php

namespace Webkul\Software\Filament\Customer\Clusters\Account\Resources\TicketResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Software\Filament\Customer\Clusters\Account\Resources\TicketResource;
use Webkul\Software\Services\TicketService;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['partner_id'] = Auth::guard('customer')->id();
        $data['status'] = 'open';

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        /** @var TicketService $service */
        $service = app(TicketService::class);

        $attachments = $data['attachments'] ?? [];
        unset($data['attachments']);

        return $service->createTicket($data, $attachments);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Your ticket has been submitted')
            ->body('Our support team will respond shortly.')
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
