<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Admin\Resources\ReferralCampaignResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Referral\Filament\Admin\Resources\ReferralCampaignResource;

class ManageReferralCampaigns extends ManageRecords
{
    protected static string $resource = ReferralCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
