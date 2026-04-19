<?php

namespace Webkul\Lead\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\Lead\Enums\LeadSource;
use Webkul\Lead\Models\Lead;

class LeadsSourceChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('leads::filament/widgets/leads-source-chart.heading');
    }

    protected function getData(): array
    {
        $sources = LeadSource::cases();

        $labels = array_map(fn ($s) => $s->getLabel(), $sources);
        $data = array_map(fn ($s) => Lead::query()->where('source', $s->value)->count(), $sources);

        return [
            'datasets' => [
                [
                    'label'           => __('leads::filament/widgets/leads-source-chart.dataset-label'),
                    'data'            => $data,
                    'backgroundColor' => [
                        '#6366f1', '#f59e0b', '#22c55e', '#ef4444',
                        '#3b82f6', '#a855f7', '#14b8a6', '#f97316',
                    ],
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
