<?php

namespace Webkul\Wifi\Filament\Customer\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Webkul\Wifi\Filament\Customer\Concerns\HasCustomerCloudAccess;
use Webkul\Wifi\Models\Voucher;

class VoucherStatusDistribution extends ChartWidget
{
    use HasCustomerCloudAccess;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'lg' => 1,
    ];

    public function getHeading(): string
    {
        return __('wifi::filament/customer/widgets/voucher-status-distribution.heading');
    }

    public function getDescription(): ?string
    {
        return __('wifi::filament/customer/widgets/voucher-status-distribution.description');
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
        $startOfMonth = Carbon::now()->startOfMonth();
        $now = Carbon::now();

        // Query current month vouchers grouped by profile
        $profileCounts = Voucher::whereIn('cloud_id', $cloudIds)
            ->whereNotNull('profile')
            ->where('profile', '!=', '')
            ->whereBetween('created', [$startOfMonth, $now])
            ->selectRaw('profile, count(*) as total')
            ->groupBy('profile')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'profile')
            ->toArray();

        // Fallback to all-time if current month has no voucher records
        if (empty($profileCounts)) {
            $profileCounts = Voucher::whereIn('cloud_id', $cloudIds)
                ->whereNotNull('profile')
                ->where('profile', '!=', '')
                ->selectRaw('profile, count(*) as total')
                ->groupBy('profile')
                ->orderByDesc('total')
                ->limit(8)
                ->pluck('total', 'profile')
                ->toArray();
        }

        $labels = [];
        $data = [];
        $colors = [
            'rgba(59, 130, 246, 0.85)',   // Blue
            'rgba(16, 185, 129, 0.85)',   // Green
            'rgba(245, 158, 11, 0.85)',   // Amber
            'rgba(139, 92, 246, 0.85)',   // Purple
            'rgba(236, 72, 153, 0.85)',   // Pink
            'rgba(20, 184, 166, 0.85)',   // Teal
            'rgba(249, 115, 22, 0.85)',   // Orange
            'rgba(107, 114, 128, 0.85)',  // Gray
        ];

        foreach ($profileCounts as $profile => $count) {
            $labels[] = "{$profile} (" . number_format($count) . ')';
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label'           => __('wifi::filament/customer/widgets/voucher-status-distribution.dataset_label'),
                    'data'            => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderWidth'     => 1,
                ],
            ],
            'labels' => $labels,
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
