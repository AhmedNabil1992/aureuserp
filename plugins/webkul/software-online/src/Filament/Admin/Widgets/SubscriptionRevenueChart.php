<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\SoftwareOnline\Models\OnlineInstanceTransaction;

class SubscriptionRevenueChart extends ChartWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl'      => 2,
    ];

    protected ?string $maxHeight = '320px';

    protected ?string $pollingInterval = '60s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_online_subscription_revenue_chart';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software-online::filament/admin/widgets/dashboard.revenue.heading');
    }

    protected function getData(): array
    {
        $months = collect(range(11, 0))
            ->map(fn (int $monthsAgo): Carbon => now()->startOfMonth()->subMonths($monthsAgo));

        $baseQuery = OnlineInstanceTransaction::query()
            ->where('status', 'paid')
            ->whereHas('partner', fn (Builder $query): Builder => $query->where('company_id', current_company_id()));

        return [
            'datasets' => [
                [
                    'label'           => __('software-online::filament/admin/widgets/dashboard.revenue.datasets.revenue'),
                    'data'            => $months->map(fn (Carbon $month): float => (float) (clone $baseQuery)
                        ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                        ->sum('amount'))->all(),
                    'backgroundColor' => '#3b82f6',
                    'borderRadius'    => 6,
                    'yAxisID'         => 'y',
                ],
                [
                    'label'       => __('software-online::filament/admin/widgets/dashboard.revenue.datasets.subscriptions'),
                    'data'        => $months->map(fn (Carbon $month): int => (clone $baseQuery)
                        ->whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                        ->count())->all(),
                    'borderColor' => '#93c5fd',
                    'pointRadius' => 3,
                    'tension'     => 0.35,
                    'type'        => 'line',
                    'yAxisID'     => 'y1',
                ],
            ],
            'labels' => $months->map(fn (Carbon $month): string => $month->translatedFormat('M Y'))->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'scales'              => [
                'y'  => ['beginAtZero' => true, 'position' => 'left'],
                'y1' => ['beginAtZero' => true, 'position' => 'right', 'grid' => ['drawOnChartArea' => false]],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
