<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource;

class CreateCannedReply extends CreateRecord
{
    protected static string $resource = CannedReplyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
