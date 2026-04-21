<?php

namespace Webkul\Lead\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\Lead\Filament\Widgets\LeadsSourceChartWidget;
use Webkul\Lead\Filament\Widgets\LeadsStatsWidget;
use Webkul\Lead\Filament\Widgets\LeadsStatusChartWidget;
use Webkul\Lead\Filament\Widgets\PendingFollowUpsWidget;

class LeadsDashboard extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = 'leads';

    protected static ?int $navigationSort = -1;

    protected static function getPagePermission(): ?string
    {
        return 'page_lead_leads_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('leads::filament/pages/dashboard.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.leads');
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-chart-bar';
    }

    public function getWidgets(): array
    {
        return [
            LeadsStatsWidget::class,
            LeadsStatusChartWidget::class,
            LeadsSourceChartWidget::class,
            PendingFollowUpsWidget::class,
        ];
    }
}
