<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource;
use Webkul\TechnicalSupport\Services\TicketService;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $filePaths = $data['attachments'] ?? [];
        unset($data['attachments']);

        $data['creator_id'] = Auth::id();

        /** @var TicketService $service */
        $service = app(TicketService::class);

        return $service->createTicket($data, $filePaths);
    }
}
