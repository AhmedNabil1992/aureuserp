<?php

namespace Webkul\SoftwareOnline\Filament\Customer\Resources\OnlineInstanceResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Webkul\SoftwareOnline\Filament\Customer\Pages\ExploreSystemsPage;
use Webkul\SoftwareOnline\Filament\Customer\Resources\OnlineInstanceResource;

class ListOnlineInstances extends ListRecords
{
    protected static string $resource = OnlineInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createNew')
                ->label(__('software-online::filament/customer/resources/my_instances.actions.create_new'))
                ->icon('heroicon-o-plus')
                ->url(ExploreSystemsPage::getUrl()),
        ];
    }
}
