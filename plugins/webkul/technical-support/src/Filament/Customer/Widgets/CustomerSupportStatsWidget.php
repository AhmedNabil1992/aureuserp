<?php

namespace Webkul\TechnicalSupport\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Models\QuickDownload;
use Webkul\TechnicalSupport\Models\Ticket;

class CustomerSupportStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $partnerId = Auth::guard('customer')->id();

        if (! $partnerId) {
            return [];
        }

        $openCount = Ticket::where('partner_id', $partnerId)
            ->where('status', TicketStatus::Open->value)
            ->count();

        $pendingCount = Ticket::where('partner_id', $partnerId)
            ->where('status', TicketStatus::Pending->value)
            ->count();

        $closedCount = Ticket::where('partner_id', $partnerId)
            ->where('status', TicketStatus::Closed->value)
            ->count();

        $downloadsCount = QuickDownload::where('is_active', true)->count();

        return [
            Stat::make('تذاكر مفتوحة', $openCount)
                ->description('تذاكر تنتظر رد الدعم الفني')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),

            Stat::make('قيد المتابعة', $pendingCount)
                ->description('تذاكر يتم العمل عليها')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('تذاكر مكتملة ومغلقة', $closedCount)
                ->description('تم حلها بنجاح')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('برامج وأدوات مساعدة', $downloadsCount)
                ->description('متاحة للتحميل المباشر')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('gray'),
        ];
    }
}
