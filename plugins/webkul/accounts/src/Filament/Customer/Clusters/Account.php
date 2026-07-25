<?php

namespace Webkul\Account\Filament\Customer\Clusters;

use Filament\Clusters\Cluster;

class Account extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.accounting');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.accounting');
    }
}
