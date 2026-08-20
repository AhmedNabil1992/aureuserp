<?php

namespace Webkul\Software\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class Licensing extends Cluster
{
    protected static ?string $slug = 'software/licensing';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/clusters/navigation.navigation.licensing');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }
}
