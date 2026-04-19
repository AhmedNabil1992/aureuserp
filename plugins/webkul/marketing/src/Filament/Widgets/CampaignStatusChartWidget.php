<?php

namespace Webkul\Marketing\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\Marketing\Enums\CampaignStatus;
use Webkul\Marketing\Models\Campaign;

class CampaignStatusChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('marketing::filament/widgets/campaign-status-chart.heading');
    }

    protected function getData(): array
    {
        $statuses = CampaignStatus::cases();

        $labels = array_map(fn ($s) => $s->getLabel(), $statuses);
        $data = array_map(fn ($s) => Campaign::query()->where('status', $s->value)->count(), $statuses);

        $colors = [
            CampaignStatus::Draft->value     => '#6b7280',
            CampaignStatus::Active->value    => '#22c55e',
            CampaignStatus::Completed->value => '#3b82f6',
            CampaignStatus::Paused->value    => '#f59e0b',
        ];

        $backgroundColors = array_map(fn ($s) => $colors[$s->value] ?? '#6b7280', $statuses);

        return [
            'datasets' => [
                [
                    'label'           => __('marketing::filament/widgets/campaign-status-chart.dataset-label'),
                    'data'            => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
