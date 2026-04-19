<?php

namespace Webkul\Marketing\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\Marketing\Filament\Widgets\CampaignStatusChartWidget;
use Webkul\Marketing\Filament\Widgets\MarketingStatsWidget;
use Webkul\Marketing\Filament\Widgets\TopCampaignsByLeadsWidget;

class MarketingDashboard extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = 'marketing';

    protected static ?int $navigationSort = -1;

    protected static function getPagePermission(): ?string
    {
        return 'page_marketing_marketing_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('marketing::filament/pages/dashboard.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('marketing::filament/pages/dashboard.navigation.group');
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-chart-pie';
    }

    public function getWidgets(): array
    {
        return [
            MarketingStatsWidget::class,
            CampaignStatusChartWidget::class,
            TopCampaignsByLeadsWidget::class,
        ];
    }
}
