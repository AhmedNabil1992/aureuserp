<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Admin\Resources\ReferralRedemptionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Referral\Filament\Admin\Resources\ReferralRedemptionResource;

class ListReferralRedemptions extends ListRecords
{
    protected static string $resource = ReferralRedemptionResource::class;
}
