<?php

namespace Webkul\Support\Filament\Resources\CityResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Support\Filament\Resources\CityResource;

class CreateCity extends CreateRecord
{
    protected static string $resource = CityResource::class;

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('support::filament/resources/city/pages/create-city.notification.title'))
            ->body(__('support::filament/resources/city/pages/create-city.notification.body'));
    }
}
