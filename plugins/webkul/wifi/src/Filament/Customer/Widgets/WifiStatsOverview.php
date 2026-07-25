<?php

namespace Webkul\Wifi\Filament\Customer\Widgets;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Wifi\Filament\Customer\Concerns\HasCustomerCloudAccess;
use Webkul\Wifi\Models\Radacct;
use Webkul\Wifi\Models\Voucher;
use Webkul\Wifi\Models\VoucherSale;

class WifiStatsOverview extends BaseWidget
{
    use HasCustomerCloudAccess;

    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        if (! $this->hasCloudAccess()) {
            return [];
        }

        $cloudIds = $this->getCustomerCloudIds();
        $realmNames = $this->getCustomerRealmNames();

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::SATURDAY);
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfWeek = Carbon::now()->addDays(7)->endOfDay();

        // Sales stats
        $totalSalesToday = VoucherSale::whereIn('cloudID', $cloudIds)
            ->whereDate('Date', $today)
            ->sum('SCount');

        $totalSalesThisWeek = VoucherSale::whereIn('cloudID', $cloudIds)
            ->whereBetween('Date', [$startOfWeek, Carbon::now()])
            ->sum('SCount');

        $totalSalesThisMonth = VoucherSale::whereIn('cloudID', $cloudIds)
            ->whereBetween('Date', [$startOfMonth, Carbon::now()])
            ->sum('SCount');

        // Connected clients
        $activeNow = $realmNames->isNotEmpty()
            ? Radacct::whereIn('realm', $realmNames)->whereNull('acctstoptime')->count()
            : 0;

        // Voucher stats
        $availableVouchers = Voucher::whereIn('cloud_id', $cloudIds)
            ->where('status', 'new')
            ->count();

        $expiringVouchers = Voucher::whereIn('cloud_id', $cloudIds)
            ->where('status', 'new')
            ->where('expire', '>', $today)
            ->where('expire', '<=', $endOfWeek)
            ->count();

        return [
            Stat::make(
                __('wifi::filament/customer/widgets/wifi-stats-overview.stats.today_sales'),
                number_format($totalSalesToday, 0)
            )
                ->description(__('wifi::filament/customer/widgets/wifi-stats-overview.stats.wifi_card'))
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('primary'),

            Stat::make(
                __('wifi::filament/customer/widgets/wifi-stats-overview.stats.week_sales'),
                number_format($totalSalesThisWeek, 0)
            )
                ->description(__('wifi::filament/customer/widgets/wifi-stats-overview.stats.wifi_card'))
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('info'),

            Stat::make(
                __('wifi::filament/customer/widgets/wifi-stats-overview.stats.month_sales'),
                number_format($totalSalesThisMonth, 0)
            )
                ->description(__('wifi::filament/customer/widgets/wifi-stats-overview.stats.wifi_card'))
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),

            Stat::make(
                __('wifi::filament/customer/widgets/wifi-stats-overview.stats.connected_clients'),
                number_format($activeNow, 0)
            )
                ->description(__('wifi::filament/customer/widgets/wifi-stats-overview.stats.client'))
                ->descriptionIcon('heroicon-o-signal')
                ->color($activeNow > 0 ? 'success' : 'gray'),

            Stat::make(
                __('wifi::filament/customer/widgets/wifi-stats-overview.stats.available_vouchers'),
                number_format($availableVouchers, 0)
            )
                ->description(__('wifi::filament/customer/widgets/wifi-stats-overview.stats.wifi_card'))
                ->descriptionIcon('heroicon-o-ticket')
                ->color('primary'),

            Stat::make(
                __('wifi::filament/customer/widgets/wifi-stats-overview.stats.expiring_vouchers'),
                number_format($expiringVouchers, 0)
            )
                ->description(__('wifi::filament/customer/widgets/wifi-stats-overview.stats.expiring_soon'))
                ->descriptionIcon('heroicon-o-clock')
                ->color($expiringVouchers > 0 ? 'warning' : 'gray'),
        ];
    }
}
