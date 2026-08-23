<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource;
use Webkul\SoftwareOnline\Services\OnlineSystemProvisioningService;

class CreateOnlineInstance extends CreateRecord
{
    protected static string $resource = OnlineInstanceResource::class;

    protected function afterCreate(): void
    {
        app(OnlineSystemProvisioningService::class)->provisionInstance($this->record);
    }
}
