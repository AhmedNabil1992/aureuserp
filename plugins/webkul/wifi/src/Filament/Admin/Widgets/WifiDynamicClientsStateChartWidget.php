<?php

namespace Webkul\Wifi\Filament\Admin\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Webkul\Wifi\Models\DynamicClient;

class WifiDynamicClientsStateChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $maxHeight = '320px';

    public function getHeading(): string
    {
        return __('wifi::filament/widgets/wifi-dynamic-clients-state-chart.heading');
    }

    protected function getData(): array
    {
        $now = Carbon::now('Africa/Cairo');

        $oneDayAgo = $now->copy()->subDay();
        $oneWeekAgo = $now->copy()->subWeek();
        $oneMonthAgo = $now->copy()->subMonth();

        $lessThanOneDay = DynamicClient::query()
            ->where('last_contact', '>=', $oneDayAgo)
            ->count();

        $moreThanOneDayLessThanOneWeek = DynamicClient::query()
            ->whereBetween('last_contact', [$oneWeekAgo, $oneDayAgo])
            ->count();

        $moreThanOneWeekLessThanOneMonth = DynamicClient::query()
            ->whereBetween('last_contact', [$oneMonthAgo, $oneWeekAgo])
            ->count();

        $moreThanOneMonth = DynamicClient::query()
            ->where('last_contact', '<', $oneMonthAgo)
            ->count();

        $data = [
            $lessThanOneDay,
            $moreThanOneDayLessThanOneWeek,
            $moreThanOneWeekLessThanOneMonth,
            $moreThanOneMonth,
        ];

        $baseLabels = [
            __('wifi::filament/widgets/wifi-dynamic-clients-state-chart.labels.less-than-one-day'),
            __('wifi::filament/widgets/wifi-dynamic-clients-state-chart.labels.one-day-to-one-week'),
            __('wifi::filament/widgets/wifi-dynamic-clients-state-chart.labels.one-week-to-one-month'),
            __('wifi::filament/widgets/wifi-dynamic-clients-state-chart.labels.more-than-one-month'),
        ];

        $labels = array_map(
            static fn (string $label, int $value): string => sprintf('%s (%d)', $label, $value),
            $baseLabels,
            $data,
        );

        return [
            'datasets' => [
                [
                    'label'           => __('wifi::filament/widgets/wifi-dynamic-clients-state-chart.dataset-label'),
                    'data'            => $data,
                    'backgroundColor' => ['#22c55e', '#eab308', '#3b82f6', '#ef4444'],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'datalabels' => [
                    'display' => true,
                    'anchor'  => 'end',
                    'align'   => 'top',
                    'color'   => '#111827',
                    'font'    => [
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
