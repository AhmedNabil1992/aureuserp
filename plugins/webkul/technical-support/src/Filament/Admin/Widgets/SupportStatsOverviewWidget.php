<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Models\Ticket;

class SupportStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $openCount = Ticket::where('status', TicketStatus::Open->value)->count();
        $pendingCount = Ticket::where('status', TicketStatus::Pending->value)->count();
        $closedCount = Ticket::where('status', TicketStatus::Closed->value)->count();
        $unreadCount = Ticket::where('is_unread_admin', true)->count();

        return [
            Stat::make(__('technical-support::filament/admin/widgets.stats.open'), $openCount)
                ->description(__('technical-support::filament/admin/widgets.stats.open_desc'))
                ->descriptionIcon('heroicon-m-lock-open')
                ->color('success'),

            Stat::make(__('technical-support::filament/admin/widgets.stats.pending'), $pendingCount)
                ->description(__('technical-support::filament/admin/widgets.stats.pending_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('technical-support::filament/admin/widgets.stats.unread'), $unreadCount)
                ->description(__('technical-support::filament/admin/widgets.stats.unread_desc'))
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('danger'),

            Stat::make(__('technical-support::filament/admin/widgets.stats.closed'), $closedCount)
                ->description(__('technical-support::filament/admin/widgets.stats.closed_desc'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('gray'),
        ];
    }
}
