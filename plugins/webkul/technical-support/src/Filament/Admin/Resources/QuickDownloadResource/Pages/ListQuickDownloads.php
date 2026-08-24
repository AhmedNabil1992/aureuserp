<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\QuickDownloadResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Webkul\TechnicalSupport\Filament\Admin\Resources\QuickDownloadResource;

class ListQuickDownloads extends ListRecords
{
    protected static string $resource = QuickDownloadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
