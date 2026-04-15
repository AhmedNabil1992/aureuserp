<?php

namespace Webkul\Software\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Webkul\Software\Models\LicenseSubscription;

class SoftwareSubscriptionTypesChartWidget extends ChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?int $sort = 5;

    protected ?string $maxHeight = '320px';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_software_subscription_types_chart_widget';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software::filament/admin/widgets/dashboard.subscription_types.heading');
    }

    protected function getData(): array
    {
        $subscriptionsByType = $this->applyCreatedAtFilters(LicenseSubscription::query())
            ->selectRaw("COALESCE(NULLIF(service_type, ''), 'unknown') as service_type, COUNT(*) as total")
            ->groupBy('service_type')
            ->orderByDesc('total')
            ->get();

        $labels = $subscriptionsByType
            ->map(fn (LicenseSubscription $subscription): string => $this->resolveServiceTypeLabel((string) $subscription->service_type))
            ->values()
            ->all();

        $data = $subscriptionsByType
            ->pluck('total')
            ->map(fn (int|string $count): int => (int) $count)
            ->values()
            ->all();

        return [
            'datasets' => [
                [
                    'label'           => __('software::filament/admin/widgets/dashboard.subscription_types.dataset_label'),
                    'data'            => $data,
                    'backgroundColor' => $this->resolveBackgroundColors(count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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

    private function resolveServiceTypeLabel(string $serviceType): string
    {
        $normalized = Str::of($serviceType)->trim()->lower()->replace([' ', '-', '/'], '_')->toString();

        $translationKey = 'software::filament/admin/widgets/dashboard.subscription_types.labels.'.$normalized;
        $translation = __($translationKey);

        if ($translation !== $translationKey) {
            return $translation;
        }

        if ($normalized === 'unknown' || $normalized === '') {
            return __('software::filament/admin/widgets/dashboard.subscription_types.labels.unknown');
        }

        return Str::of($serviceType)->replace(['_', '-'], ' ')->title()->toString();
    }

    private function resolveBackgroundColors(int $count): array
    {
        $palette = [
            '#0ea5e9',
            '#22c55e',
            '#f59e0b',
            '#a855f7',
            '#ef4444',
            '#14b8a6',
            '#f97316',
            '#6366f1',
        ];

        if ($count <= 0) {
            return [];
        }

        $colors = [];
        $paletteCount = count($palette);

        for ($index = 0; $index < $count; $index++) {
            $colors[] = $palette[$index % $paletteCount];
        }

        return $colors;
    }
}
