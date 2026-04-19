<?php

namespace Webkul\Lead\Filament\Resources\LeadResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Lead\Filament\Resources\LeadResource;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource),
            ViewAction::make(),
            RestoreAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
        ];
    }
}
