<?php

use Webkul\Wifi\Filament\Admin\Resources\CloudResource;
use Webkul\Wifi\Filament\Admin\Resources\DynamicClientResource;
use Webkul\Wifi\Filament\Admin\Resources\WifiPackageResource;
use Webkul\Wifi\Filament\Admin\Resources\WifiPurchaseResource;
use Webkul\Wifi\Filament\Admin\Resources\WifiVoucherBatchResource;
use Webkul\Wifi\Filament\Admin\Resources\PermanentUserResource;
use Webkul\Wifi\Filament\Admin\Resources\VoucherResource;
use Webkul\Wifi\Filament\Admin\Resources\WifiPartnerCloudResource;

return [
    'resources' => [
        'manage' => [
            CloudResource::class            => ['view_any', 'view'],
            DynamicClientResource::class    => ['view_any', 'view'],
            PermanentUserResource::class    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            VoucherResource::class          => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            WifiPackageResource::class      => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            WifiPartnerCloudResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            WifiPurchaseResource::class     => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            WifiVoucherBatchResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
        ],
    ],
];
