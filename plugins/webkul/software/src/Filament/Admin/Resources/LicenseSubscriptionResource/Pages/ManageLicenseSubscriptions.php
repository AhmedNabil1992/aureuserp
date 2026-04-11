<?php

namespace Webkul\Software\Filament\Admin\Resources\LicenseSubscriptionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\LicenseSubscriptionResource;

class ManageLicenseSubscriptions extends ManageRecords
{
    protected static string $resource = LicenseSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Subscription')->icon('heroicon-o-plus-circle'),
        ];
    }
}
