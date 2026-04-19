<?php

namespace Webkul\Lead\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\Lead\Enums\LeadStatus;
use Webkul\Lead\Models\Lead;

class LeadsStatusChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('leads::filament/widgets/leads-status-chart.heading');
    }

    protected function getData(): array
    {
        $statuses = LeadStatus::cases();

        $labels = array_map(fn ($s) => $s->getLabel(), $statuses);
        $data = array_map(fn ($s) => Lead::query()->where('status', $s->value)->count(), $statuses);

        $colors = [
            LeadStatus::New->value       => '#3b82f6',
            LeadStatus::Contacted->value => '#f59e0b',
            LeadStatus::Qualified->value => '#8b5cf6',
            LeadStatus::Converted->value => '#22c55e',
            LeadStatus::Rejected->value  => '#ef4444',
        ];

        $backgroundColors = array_map(fn ($s) => $colors[$s->value] ?? '#6b7280', $statuses);

        return [
            'datasets' => [
                [
                    'label'           => __('leads::filament/widgets/leads-status-chart.dataset-label'),
                    'data'            => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
