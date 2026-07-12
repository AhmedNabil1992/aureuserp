<?php

namespace Webkul\Software\Filament\Customer\Resources\LicenseResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Software\Filament\Customer\Resources\LicenseResource;

class ListLicenses extends ListRecords
{
    protected static string $resource = LicenseResource::class;

    public function getTitle(): string
    {
        return __('software::filament/customer/license.pages.list.title');
    }
}
