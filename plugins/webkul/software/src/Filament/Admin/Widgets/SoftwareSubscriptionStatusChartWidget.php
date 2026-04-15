<?php

namespace Webkul\Software\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Software\Models\LicenseSubscription;

class SoftwareSubscriptionStatusChartWidget extends ChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected ?string $maxHeight = '320px';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_software_subscription_status_chart_widget';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software::filament/admin/widgets/dashboard.subscription_chart.heading');
    }

    protected function getData(): array
    {
        $activeCount = $this->applyCreatedAtFilters(LicenseSubscription::query())
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->count();

        $expiredCount = $this->applyCreatedAtFilters(LicenseSubscription::query())
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', now()->toDateString())
            ->count();

        $inactiveCount = $this->applyCreatedAtFilters(LicenseSubscription::query())
            ->where('is_active', false)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->count();

        return [
            'datasets' => [
                [
                    'label'           => __('software::filament/admin/widgets/dashboard.subscription_chart.dataset_label'),
                    'data'            => [$activeCount, $inactiveCount, $expiredCount],
                    'backgroundColor' => ['#22c55e', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' => [
                __('software::filament/admin/widgets/dashboard.subscription_chart.labels.active'),
                __('software::filament/admin/widgets/dashboard.subscription_chart.labels.inactive'),
                __('software::filament/admin/widgets/dashboard.subscription_chart.labels.expired'),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    private function applyCreatedAtFilters(Builder $query): Builder
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        return $query
            ->when(
                filled($startDate),
                fn (Builder $builder): Builder => $builder->where('created_at', '>=', Carbon::parse($startDate)->startOfDay())
            )
            ->when(
                filled($endDate),
                fn (Builder $builder): Builder => $builder->where('created_at', '<=', Carbon::parse($endDate)->endOfDay())
            );
    }
}
