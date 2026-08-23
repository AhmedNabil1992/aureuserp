<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource;
use Webkul\TechnicalSupport\Filament\Admin\Widgets\SupportStatsOverviewWidget;
use Webkul\TechnicalSupport\Filament\Admin\Widgets\SupportTicketStatusChartWidget;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SupportStatsOverviewWidget::class,
            SupportTicketStatusChartWidget::class,
        ];
    }
}
