<?php

use Webkul\Article\Filament\Admin\Resources\ArticleResource;
use Webkul\Article\Filament\Admin\Resources\CategoryResource;
use Webkul\Article\Filament\Admin\Resources\TagResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any', 'force_delete', 'force_delete_any', 'restore', 'restore_any'];

return [
    'resources' => [
        'manage' => [
            ArticleResource::class  => [...$basic, ...$delete],
            CategoryResource::class => [...$basic, ...$delete],
            TagResource::class      => [...$basic, ...$delete],
        ],
    ],
];
