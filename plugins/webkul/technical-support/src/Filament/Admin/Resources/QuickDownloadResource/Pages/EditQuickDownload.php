<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\QuickDownloadResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Webkul\TechnicalSupport\Filament\Admin\Resources\QuickDownloadResource;

class EditQuickDownload extends EditRecord
{
    protected static string $resource = QuickDownloadResource::class;

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
