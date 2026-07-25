<?php

namespace Webkul\Account\Filament\Customer\Pages;

use Webkul\Account\Filament\Customer\Widgets\AvailableBalanceWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = 'dashboard';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.dashboard');
    }

    public function getWidgets(): array
    {
        return [
            AvailableBalanceWidget::class,
        ];
    }
}