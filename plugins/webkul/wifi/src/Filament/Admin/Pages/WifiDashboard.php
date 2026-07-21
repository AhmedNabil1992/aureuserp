<?php

namespace Webkul\Wifi\Filament\Admin\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Wifi\Filament\Admin\Widgets\WifiDynamicClientsStateChartWidget;
use Webkul\Wifi\Filament\Admin\Widgets\WifiPackagesChartWidget;
use Webkul\Wifi\Filament\Admin\Widgets\WifiStatsWidget;
use Webkul\Support\Enums\NavigationGroup;

class WifiDashboard extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = 'wifi';

    protected static ?int $navigationSort = -1;

    protected static function getPagePermission(): ?string
    {
        return 'page_wifi_wifi_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/pages/dashboard.navigation.title');
    }

    public static function getNavigationGroup(): \UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-chart-bar';
    }

    public function getWidgets(): array
    {
        return [
            WifiStatsWidget::class,
            WifiPackagesChartWidget::class,
            WifiDynamicClientsStateChartWidget::class,
        ];
    }
}
