<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Admin\Resources\ReferralCodeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Referral\Filament\Admin\Resources\ReferralCodeResource;

class ManageReferralCodes extends ManageRecords
{
    protected static string $resource = ReferralCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
