<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Admin\Resources\ReferralWalletTransactionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Referral\Filament\Admin\Resources\ReferralWalletTransactionResource;

class ListReferralWalletTransactions extends ListRecords
{
    protected static string $resource = ReferralWalletTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
