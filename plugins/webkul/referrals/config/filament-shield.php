<?php

use Webkul\Referral\Filament\Admin\Resources\ReferralCampaignResource;
use Webkul\Referral\Filament\Admin\Resources\ReferralCodeResource;
use Webkul\Referral\Filament\Admin\Resources\ReferralRedemptionResource;
use Webkul\Referral\Filament\Admin\Resources\ReferralWalletTransactionResource;

$readWrite = ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'];

return [
    'resources' => [
        'manage' => [
            ReferralCampaignResource::class          => $readWrite,
            ReferralCodeResource::class              => $readWrite,
            ReferralRedemptionResource::class        => ['view_any', 'view'],
            ReferralWalletTransactionResource::class => ['view_any', 'view'],
        ],
        'exclude' => [],
    ],
    'pages' => [
        'exclude' => [],
    ],
];
