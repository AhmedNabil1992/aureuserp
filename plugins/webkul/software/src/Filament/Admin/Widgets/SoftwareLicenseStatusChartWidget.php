<?php

namespace Webkul\Software\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Software\Enums\LicenseStatus;
use Webkul\Software\Models\License;

class SoftwareLicenseStatusChartWidget extends ChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '320px';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_software_license_status_chart_widget';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software::filament/admin/widgets/dashboard.license_chart.heading');
    }

    protected function getData(): array
    {
        $labels = [
            ucfirst(LicenseStatus::Pending->value),
            ucfirst(LicenseStatus::Approved->value),
            ucfirst(LicenseStatus::Rejected->value),
        ];

        $data = [
            $this->applyCreatedAtFilters(License::query())
                ->where('status', LicenseStatus::Pending->value)
                ->count(),
            $this->applyCreatedAtFilters(License::query())
                ->where('status', LicenseStatus::Approved->value)
                ->count(),
            $this->applyCreatedAtFilters(License::query())
                ->where('status', LicenseStatus::Rejected->value)
                ->count(),
        ];

        return [
            'datasets' => [
                [
                    'label'           => __('software::filament/admin/widgets/dashboard.license_chart.dataset_label'),
                    'data'            => $data,
                    'backgroundColor' => ['#f59e0b', '#22c55e', '#ef4444'],
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
}
