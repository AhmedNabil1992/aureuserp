<?php

namespace Webkul\Software\Filament\Admin\Clusters;

use Filament\Clusters\Cluster;

class Catalog extends Cluster
{
    protected static ?string $slug = 'software/catalog';

    protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return 'Catalog';
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }
}
