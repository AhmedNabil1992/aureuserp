<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource;

class ListCannedReplies extends ListRecords
{
    protected static string $resource = CannedReplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
