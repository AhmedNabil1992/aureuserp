<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Models\OnlineInstance;

class InstanceStatusChart extends ChartWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl'      => 1,
    ];

    protected ?string $maxHeight = '320px';

    protected ?string $pollingInterval = '60s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_online_instance_status_chart';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software-online::filament/admin/widgets/dashboard.status.heading');
    }

    protected function getData(): array
    {
        $query = OnlineInstance::query()
            ->whereHas('partner', fn (Builder $builder): Builder => $builder->where('company_id', current_company_id()));

        $groups = [
            'active'    => [InstanceStatus::Active],
            'awaiting'  => [InstanceStatus::Pending, InstanceStatus::Provisioning],
            'suspended' => [InstanceStatus::Suspended],
            'expired'   => [InstanceStatus::Expired],
            'failed'    => [InstanceStatus::Failed, InstanceStatus::Deleting],
        ];

        return [
            'datasets' => [[
                'data' => collect($groups)
                    ->map(fn (array $statuses): int => (clone $query)->whereIn(
                        'status',
                        array_map(fn (InstanceStatus $status): string => $status->value, $statuses),
                    )->count())
                    ->values()
                    ->all(),
                'backgroundColor' => ['#22c55e', '#f59e0b', '#ef4444', '#94a3b8', '#dc2626'],
                'borderWidth'     => 0,
            ]],
            'labels' => collect(array_keys($groups))
                ->map(fn (string $group): string => __('software-online::filament/admin/widgets/dashboard.status.labels.'.$group))
                ->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'cutout'              => '68%',
            'maintainAspectRatio' => false,
            'plugins'             => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
