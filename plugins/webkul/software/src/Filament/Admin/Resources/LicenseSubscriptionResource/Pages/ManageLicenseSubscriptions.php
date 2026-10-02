<?php

namespace Webkul\Software\Filament\Admin\Resources\LicenseSubscriptionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Software\Filament\Admin\Resources\LicenseSubscriptionResource;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseSubscription;
use Webkul\Software\Services\LicenseSubscriptionBillingService;

class ManageLicenseSubscriptions extends ManageRecords
{
    protected static string $resource = LicenseSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('software::filament/admin/resources/license-subscription.titles.create'))
                ->icon('heroicon-o-plus-circle')
                ->using(function (array $data): LicenseSubscription {
                    $result = app(LicenseSubscriptionBillingService::class)->subscribeOrRenew(
                        License::query()->findOrFail($data['license_id']),
                        (int) $data['feature_id'],
                        false,
                    );

                    return $result['subscription'];
                }),
        ];
    }
}
