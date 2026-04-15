<?php

namespace Webkul\Software\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Software\Models\LicenseSubscription;

class SoftwareSubscriptionsAlertsOverviewWidget extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '30s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_software_subscriptions_alerts_overview_widget';
    }

    protected function getHeading(): ?string
    {
        return __('software::filament/admin/widgets/dashboard.subscription_alerts.heading');
    }

    protected function getStats(): array
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();
        $sevenDaysLater = now()->addDays(7)->toDateString();

        $expiringThisMonthCount = LicenseSubscription::query()
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$startOfMonth, $endOfMonth])
            ->count();

        $expiringWithin7DaysCount = LicenseSubscription::query()
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$today, $sevenDaysLater])
            ->count();

        $expiredThisMonthCount = LicenseSubscription::query()
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$startOfMonth, $today])
            ->count();

        return [
            Stat::make(
                __('software::filament/admin/widgets/dashboard.subscription_alerts.expiring_this_month.label'),
                $expiringThisMonthCount
            )
                ->description(__('software::filament/admin/widgets/dashboard.subscription_alerts.expiring_this_month.description'))
                ->color('warning'),

            Stat::make(
                __('software::filament/admin/widgets/dashboard.subscription_alerts.expiring_within_7_days.label'),
                $expiringWithin7DaysCount
            )
                ->description(__('software::filament/admin/widgets/dashboard.subscription_alerts.expiring_within_7_days.description'))
                ->color('danger'),

            Stat::make(
                __('software::filament/admin/widgets/dashboard.subscription_alerts.expired_this_month.label'),
                $expiredThisMonthCount
            )
                ->description(__('software::filament/admin/widgets/dashboard.subscription_alerts.expired_this_month.description'))
                ->color('gray'),
        ];
    }
}
