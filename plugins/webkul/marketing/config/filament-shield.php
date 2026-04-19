<?php

use Webkul\Marketing\Filament\Resources\CampaignResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any', 'force_delete', 'force_delete_any', 'restore', 'restore_any'];

return [
    'resources' => [
        'manage' => [
            CampaignResource::class => [...$basic, ...$delete],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [],
    ],

    'widgets' => [
        'exclude' => [],
    ],
];
