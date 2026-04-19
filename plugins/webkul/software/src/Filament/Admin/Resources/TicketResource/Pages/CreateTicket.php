<?php

namespace Webkul\Software\Filament\Admin\Resources\TicketResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Software\Filament\Admin\Resources\TicketResource;
use Webkul\Software\Services\TicketService;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

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
            ->title('Ticket created')
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
