<?php

namespace Webkul\Software\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Software\Enums\TicketStatus;
use Webkul\Software\Models\Ticket;

class SoftwareTicketStatusChartWidget extends ChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '320px';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_software_ticket_status_chart_widget';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software::filament/admin/widgets/dashboard.ticket_chart.heading');
    }

    protected function getData(): array
    {
        $labels = [
            ucfirst(TicketStatus::Open->value),
            ucfirst(TicketStatus::Pending->value),
            ucfirst(TicketStatus::Closed->value),
        ];

        $data = [
            $this->applyCreatedAtFilters(Ticket::query())
                ->where('status', TicketStatus::Open->value)
                ->count(),
            $this->applyCreatedAtFilters(Ticket::query())
                ->where('status', TicketStatus::Pending->value)
                ->count(),
            $this->applyCreatedAtFilters(Ticket::query())
                ->where('status', TicketStatus::Closed->value)
                ->count(),
        ];

        return [
            'datasets' => [
                [
                    'label'           => __('software::filament/admin/widgets/dashboard.ticket_chart.dataset_label'),
                    'data'            => $data,
                    'backgroundColor' => ['#3b82f6', '#f59e0b', '#22c55e'],
                ],
            ],
            'labels' => $labels,
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
