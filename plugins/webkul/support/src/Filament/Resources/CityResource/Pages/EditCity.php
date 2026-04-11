<?php

namespace Webkul\Support\Filament\Resources\CityResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Support\Filament\Resources\CityResource;

class EditCity extends EditRecord
{
    protected static string $resource = CityResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('support::filament/resources/city/pages/edit-city.notification.title'))
            ->body(__('support::filament/resources/city/pages/edit-city.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/city/pages/edit-city.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/city/pages/edit-city.header-actions.delete.notification.body')),
                ),
        ];
    }
}
