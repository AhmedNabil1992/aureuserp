<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineSystemResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineSystemResource;

class EditOnlineSystem extends EditRecord
{
    protected static string $resource = OnlineSystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
