<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource;

class EditCannedReply extends EditRecord
{
    protected static string $resource = CannedReplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
