<?php

namespace Webkul\Software\Filament\Customer\Clusters\Account\Resources\LicenseResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Software\Filament\Customer\Clusters\Account\Resources\LicenseResource;

class ListLicenses extends ListRecords
{
    protected static string $resource = LicenseResource::class;

    public function getTitle(): string
    {
        return __('تراخيص البرامج');
    }
}
