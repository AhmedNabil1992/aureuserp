<?php

namespace Webkul\Wifi\Filament\Admin\Resources\DynamicClientResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Webkul\Wifi\Filament\Admin\Resources\DynamicClientResource;

class ListDynamicClients extends ListRecords
{
    protected static string $resource = DynamicClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\Action::make('access_points_only')
            //     ->label('Access Points Only')
            //     ->action(fn () => null),
        ];
    }
}
