<?php

namespace Webkul\Software\Filament\Admin\Resources\TicketResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Software\Filament\Admin\Resources\TicketResource;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Ticket')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
