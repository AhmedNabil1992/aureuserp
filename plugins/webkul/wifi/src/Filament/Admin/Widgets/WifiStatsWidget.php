<?php

namespace Webkul\Wifi\Filament\Admin\Widgets;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\DynamicClient;
use Webkul\Wifi\Models\PermanentUser;
use Webkul\Wifi\Models\Radacct;
use Webkul\Wifi\Models\VoucherSale;
use Webkul\Wifi\Models\WifiPurchase;
use Webkul\Wifi\Models\WifiVoucherBatch;

class WifiStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = Carbon::now('Africa/Cairo');

        $startOfToday = $now->copy()->startOfDay();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::SATURDAY);
        $startOfMonth = $now->copy()->startOfMonth();

        $voucherSalesToday = (int) VoucherSale::query()
            ->whereBetween('Date', [$startOfToday, $now])
            ->sum('SCount');

        $voucherSalesThisWeek = (int) VoucherSale::query()
            ->whereBetween('Date', [$startOfWeek, $now])
            ->sum('SCount');

        $voucherSalesThisMonth = (int) VoucherSale::query()
            ->whereBetween('Date', [$startOfMonth, $now])
            ->sum('SCount');

        return [
            Stat::make(
                __('wifi::filament/widgets/wifi-stats.stats.total-purchases'),
                WifiPurchase::query()->whereBetween('created_at', [$startOfMonth, $now])->count()
            )->color('info'),

            Stat::make(
                __('wifi::filament/widgets/wifi-stats.stats.total-voucher-batches'),
                WifiVoucherBatch::query()->whereBetween('created_at', [$startOfMonth, $now])->count()
            )->color('primary'),

            Stat::make(
                __('wifi::filament/widgets/wifi-stats.stats.total-clouds'),
                Cloud::query()->count()
            )->color('warning'),

            Stat::make(
                __('wifi::filament/widgets/wifi-stats.stats.total-dynamic-clients'),
                DynamicClient::query()->count()
            )->color('danger'),

            Stat::make(
                __('wifi::filament/widgets/wifi-stats.stats.total-permanent-users'),
                PermanentUser::query()->count()
            )->color('success'),

            Stat::make(
                __('wifi::filament/widgets/wifi-stats.stats.voucher-sales-today'),
                $voucherSalesToday
            )->color('success'),

            Stat::make(
                __('wifi::filament/widgets/wifi-stats.stats.voucher-sales-week'),
                $voucherSalesThisWeek
            )->color('info'),

            Stat::make(
                __('wifi::filament/widgets/wifi-stats.stats.voucher-sales-month'),
                $voucherSalesThisMonth
            )->color('primary'),

            Stat::make(
                __('wifi::filament/widgets/wifi-stats.stats.active-now'),
                Radacct::query()->whereNull('acctstoptime')->count()
            )->color('success'),
        ];
    }
}
