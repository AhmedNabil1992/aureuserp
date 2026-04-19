<?php

namespace Webkul\Wifi\Filament\Admin\Resources\CloudResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Webkul\Wifi\Filament\Admin\Resources\CloudResource;

class ListClouds extends ListRecords
{
    protected static string $resource = CloudResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->action(fn () => null),
        ];
    }
}
