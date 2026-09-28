<?php

use Webkul\Vpn\Filament\Admin\Resources\VpnServerResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];

return [
    'resources' => [
        'manage' => [
            VpnServerResource::class => [...$basic, ...$delete],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [],
    ],
];
