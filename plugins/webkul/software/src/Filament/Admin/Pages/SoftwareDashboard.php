<?php

namespace Webkul\Software\Filament\Admin\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Software\Filament\Admin\Widgets\SoftwareLicenseStatusChartWidget;
use Webkul\Software\Filament\Admin\Widgets\SoftwareStatsOverviewWidget;
use Webkul\Software\Filament\Admin\Widgets\SoftwareSubscriptionsAlertsOverviewWidget;
use Webkul\Software\Filament\Admin\Widgets\SoftwareSubscriptionsExpiringThisMonthWidget;
use Webkul\Software\Filament\Admin\Widgets\SoftwareSubscriptionStatusChartWidget;
use Webkul\Software\Filament\Admin\Widgets\SoftwareSubscriptionTypesChartWidget;
use Webkul\Software\Filament\Admin\Widgets\SoftwareTicketStatusChartWidget;
use Webkul\Software\Filament\Admin\Widgets\SoftwareTopProgramsWidget;

class SoftwareDashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

    protected static string $routePath = 'software';

    protected static function getPagePermission(): ?string
    {
        return 'page_software_software_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/pages/dashboard.navigation_label');
    }

    public static function getNavigationGroup(): string
    {
        return 'Dashboard';
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'md'      => 2,
                        'xl'      => 4,
                    ])
                    ->schema([
                        DatePicker::make('startDate')
                            ->label(__('software::filament/admin/pages/dashboard.filters.start_date'))
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->native(false),
                        DatePicker::make('endDate')
                            ->label(__('software::filament/admin/pages/dashboard.filters.end_date'))
                            ->minDate(fn (Get $get) => $get('startDate') ?: now()->subYear())
                            ->maxDate(now())
                            ->native(false),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            SoftwareStatsOverviewWidget::class,
            SoftwareLicenseStatusChartWidget::class,
            SoftwareTicketStatusChartWidget::class,
            SoftwareSubscriptionStatusChartWidget::class,
            SoftwareSubscriptionTypesChartWidget::class,
            SoftwareSubscriptionsAlertsOverviewWidget::class,
            SoftwareSubscriptionsExpiringThisMonthWidget::class,
            SoftwareTopProgramsWidget::class,
        ];
    }
}
