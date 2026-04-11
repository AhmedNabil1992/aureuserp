<?php

namespace Webkul\Support\Filament\Resources\CityResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Support\Filament\Resources\CityResource;

class ListCities extends ListRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('support::filament/resources/city/pages/list-city.header-actions.create.label')),
        ];
    }
}
