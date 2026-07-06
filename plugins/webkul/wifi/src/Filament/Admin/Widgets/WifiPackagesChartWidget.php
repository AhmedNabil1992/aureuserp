<?php

namespace Webkul\Wifi\Filament\Admin\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Webkul\Wifi\Models\WifiPackage;
use Webkul\Wifi\Models\WifiPurchase;

class WifiPackagesChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('wifi::filament/widgets/wifi-packages-chart.heading');
    }

    protected function getData(): array
    {
        $now = Carbon::now('Africa/Cairo');
        $startOfMonth = $now->copy()->startOfMonth();

        $packages = WifiPackage::query()
            ->with('product')
            ->orderByDesc('id')
            ->get();

        $salesByPackage = WifiPurchase::query()
            ->selectRaw('wifi_package_id, COUNT(quantity) as total_quantity')
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->groupBy('wifi_package_id')
            ->pluck('total_quantity', 'wifi_package_id');

        $labels = $packages
            ->map(fn (WifiPackage $package): string => $package->display_name)
            ->all();

        $data = $packages
            ->map(fn (WifiPackage $package): int => (int) ($salesByPackage[$package->id] ?? 0))
            ->all();

        $backgroundColor = $packages
            ->map(fn (): string => '#3b82f6')
            ->all();

        return [
            'datasets' => [
                [
                    'label'           => __('wifi::filament/widgets/wifi-packages-chart.dataset-label'),
                    'data'            => $data,
                    'backgroundColor' => $backgroundColor,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
