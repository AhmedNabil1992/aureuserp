<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlinePlanResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlinePlanResource;

class EditOnlinePlan extends EditRecord
{
    protected static string $resource = OnlinePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
