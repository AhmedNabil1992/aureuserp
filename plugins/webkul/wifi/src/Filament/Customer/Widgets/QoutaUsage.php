<?php

namespace Webkul\Wifi\Filament\Customer\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Webkul\Wifi\Filament\Customer\Concerns\HasCustomerCloudAccess;
use Webkul\Wifi\Models\Radacct;

class QoutaUsage extends ChartWidget
{
    use HasCustomerCloudAccess;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('wifi::filament/customer/widgets/quota-usage.heading');
    }

    public function getDescription(): ?string
    {
        return __('wifi::filament/customer/widgets/quota-usage.description');
    }

    protected function getData(): array
    {
        if (! $this->hasCloudAccess()) {
            return [
                'datasets' => [],
                'labels'   => [],
            ];
        }

        $realmNames = $this->getCustomerRealmNames();

        if ($realmNames->isEmpty()) {
            return [
                'datasets' => [],
                'labels'   => [],
            ];
        }

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        $results = Radacct::query()
            ->whereIn('realm', $realmNames)
            ->whereBetween('acctstarttime', [$startDate, $endDate])
            ->selectRaw('DATE(acctstarttime) as date')
            ->selectRaw('SUM(acctoutputoctets + acctinputoctets) / 1073741824 as usage_gb')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('D d');
            $data[] = round((float) ($results[$dateStr]->usage_gb ?? 0), 2);
        }

        return [
            'datasets' => [
                [
                    'label'           => __('wifi::filament/customer/widgets/quota-usage.dataset_label'),
                    'data'            => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.5)',
                        'rgba(16, 185, 129, 0.5)',
                        'rgba(245, 158, 11, 0.5)',
                        'rgba(239, 68, 68, 0.5)',
                        'rgba(139, 92, 246, 0.5)',
                        'rgba(236, 72, 153, 0.5)',
                        'rgba(20, 184, 166, 0.5)',
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(20, 184, 166, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => '(value) => value + " GB"',
                    ],
                ],
            ],
        ];
    }
}
