<?php

namespace Webkul\TechnicalSupport\Filament\Customer\Resources\TicketResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\TechnicalSupport\Filament\Customer\Resources\TicketResource;
use Webkul\TechnicalSupport\Services\TicketService;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $filePaths = $data['attachments'] ?? [];
        unset($data['attachments']);

        if (!empty($data['voice_note'])) {
            $audioData = substr($data['voice_note'], strpos($data['voice_note'], ',') + 1);
            $audioDecoded = base64_decode($audioData);

            $fileName = 'technical-support/tickets/voice_' . time() . '_' . uniqid() . '.webm';
            Storage::disk('public')->put($fileName, $audioDecoded);
            $filePaths[] = $fileName;
            unset($data['voice_note']);
        }

        $data['partner_id'] = Auth::guard('customer')->id();

        if (empty(trim(strip_tags($data['content'] ?? '')))) {
            $data['content'] = !empty($filePaths) ? 'مرفقات / تسجيل صوتي' : ($data['title'] ?? 'تفاصيل المشكلة');
        }

        /** @var TicketService $service */
        $service = app(TicketService::class);

        return $service->createTicket($data, $filePaths);
    }
}
