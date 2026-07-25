<?php

namespace Webkul\Wifi\Filament\Customer\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Webkul\Wifi\Filament\Customer\Concerns\HasCustomerCloudAccess;
use Webkul\Wifi\Models\DynamicClient;

class AccessPointsStatus extends ChartWidget
{
    use HasCustomerCloudAccess;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'lg' => 1,
    ];

    public function getHeading(): string
    {
        return __('wifi::filament/customer/widgets/access-points-status.heading');
    }

    public function getDescription(): ?string
    {
        return __('wifi::filament/customer/widgets/access-points-status.description');
    }

    protected function getData(): array
    {
        if (! $this->hasCloudAccess()) {
            return [
                'datasets' => [],
                'labels'   => [],
            ];
        }

        $cloudIds = $this->getCustomerCloudIds();

        $aps = DynamicClient::whereIn('cloud_id', $cloudIds)->get();

        $onlineCount = 0;
        $offlineCount = 0;
        $now = Carbon::now();

        foreach ($aps as $ap) {
            if ($ap->last_contact && Carbon::parse($ap->last_contact)->diffInMinutes($now) <= 30) {
                $onlineCount++;
            } else {
                $offlineCount++;
            }
        }

        return [
            'datasets' => [
                [
                    'label'           => __('wifi::filament/customer/widgets/access-points-status.dataset_label'),
                    'data'            => [$onlineCount, $offlineCount],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.85)',   // Green - Online
                        'rgba(239, 68, 68, 0.85)',   // Red - Offline
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                __('wifi::filament/customer/widgets/access-points-status.statuses.online') . " ({$onlineCount})",
                __('wifi::filament/customer/widgets/access-points-status.statuses.offline') . " ({$offlineCount})",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
