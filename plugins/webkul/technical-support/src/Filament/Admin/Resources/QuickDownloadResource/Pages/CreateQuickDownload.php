<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\QuickDownloadResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\TechnicalSupport\Filament\Admin\Resources\QuickDownloadResource;

class CreateQuickDownload extends CreateRecord
{
    protected static string $resource = QuickDownloadResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
