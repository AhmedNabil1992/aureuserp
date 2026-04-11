<?php

namespace Webkul\Software\Filament\Admin\Resources\TicketEventResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\TicketEventResource;

class ManageTicketEvents extends ManageRecords
{
    protected static string $resource = TicketEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Event')->icon('heroicon-o-plus-circle'),
        ];
    }
}
