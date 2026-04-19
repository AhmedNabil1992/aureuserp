<?php

use Webkul\Wifi\Filament\Admin\Resources\CloudResource;
use Webkul\Wifi\Filament\Admin\Resources\DynamicClientResource;
use Webkul\Wifi\Filament\Admin\Resources\WifiPackageResource;
use Webkul\Wifi\Filament\Admin\Resources\WifiPurchaseResource;
use Webkul\Wifi\Filament\Admin\Resources\WifiVoucherBatchResource;

return [
    'resources' => [
        'manage' => [
            CloudResource::class            => ['view_any', 'view'],
            DynamicClientResource::class    => ['view_any', 'view'],
            WifiPackageResource::class      => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            WifiPurchaseResource::class     => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            WifiVoucherBatchResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
        ],
    ],
];
