<?php

namespace Webkul\Software\Filament\Admin\Resources\TicketResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Software\Filament\Admin\Resources\TicketResource;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Do not populate the virtual attachments field from DB
        $data['attachments'] = [];

        return $data;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Ticket updated')
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
