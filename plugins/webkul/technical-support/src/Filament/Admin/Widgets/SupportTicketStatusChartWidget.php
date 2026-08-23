<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Models\Ticket;

class SupportTicketStatusChartWidget extends ChartWidget
{
    protected ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('technical-support::filament/admin/widgets.charts.status_heading');
    }

    protected function getData(): array
    {
        $open = Ticket::where('status', TicketStatus::Open->value)->count();
        $pending = Ticket::where('status', TicketStatus::Pending->value)->count();
        $closed = Ticket::where('status', TicketStatus::Closed->value)->count();

        return [
            'datasets' => [
                [
                    'label'           => 'Tickets',
                    'data'            => [$open, $pending, $closed],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                ],
            ],
            'labels' => [
                TicketStatus::Open->getLabel(),
                TicketStatus::Pending->getLabel(),
                TicketStatus::Closed->getLabel(),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
