<?php

namespace Webkul\Software\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class Support extends Cluster
{
    protected static ?string $slug = 'software/support';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/clusters/navigation.navigation.support');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }
}
