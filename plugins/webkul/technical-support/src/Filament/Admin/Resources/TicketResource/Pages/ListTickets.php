<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;
use Webkul\TechnicalSupport\Enums\TicketPriority;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketResource;
use Webkul\TechnicalSupport\Filament\Admin\Widgets\SupportStatsOverviewWidget;
use Webkul\TechnicalSupport\Filament\Admin\Widgets\SupportTicketStatusChartWidget;

class ListTickets extends ListRecords
{
    use HasTableViews;

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

    public function getPresetTableViews(): array
    {
        return [
            'my_tickets' => PresetView::make(__('technical-support::filament/admin/resources/ticket.tabs.my_tickets'))
                ->icon('heroicon-m-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_to', Auth::id())),

            'open' => PresetView::make(__('technical-support::filament/admin/resources/ticket.tabs.open'))
                ->icon('heroicon-m-lock-open')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatus::Open)),

            'pending' => PresetView::make(__('technical-support::filament/admin/resources/ticket.tabs.pending'))
                ->icon('heroicon-m-clock')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatus::Pending)),

            'unread' => PresetView::make(__('technical-support::filament/admin/resources/ticket.tabs.unread'))
                ->icon('heroicon-m-envelope')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_unread_admin', true)),

            'urgent' => PresetView::make(__('technical-support::filament/admin/resources/ticket.tabs.urgent'))
                ->icon('heroicon-m-exclamation-triangle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('priority', [TicketPriority::High, TicketPriority::Urgent])),

            'unassigned' => PresetView::make(__('technical-support::filament/admin/resources/ticket.tabs.unassigned'))
                ->icon('heroicon-m-user-minus')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('assigned_to')->where('status', '!=', TicketStatus::Closed)),

            'closed' => PresetView::make(__('technical-support::filament/admin/resources/ticket.tabs.closed'))
                ->icon('heroicon-m-lock-closed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', TicketStatus::Closed)),

            'archived' => PresetView::make(__('technical-support::filament/admin/resources/ticket.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }

    protected function getListeners(): array
    {
        return [
            'echo-private:tickets.admin-sidebar,.TicketMessageSent' => '$refresh',
        ];
    }
}
