<?php

namespace Webkul\Account\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Models\BalanceRequest;
use Webkul\Account\Models\CustomerCredit;

class BalanceWidget extends BaseWidget
{
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'العملاء';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return Auth::guard('customer')->check();
    }

    protected function getStats(): array
    {
        $partnerId = Auth::guard('customer')->id();

        // Get or create customer credit
        $credit = CustomerCredit::firstOrCreate(
            ['partner_id' => $partnerId],
            [
                'balance'         => 0,
                'reserved_amount' => 0,
                'status'          => 'active',
            ]
        );

        // Get pending requests count
        $pendingRequestsCount = BalanceRequest::where('partner_id', $partnerId)
            ->where('status', BalanceRequest::STATUS_PENDING)
            ->count();

        // Get approved requests count (total this month)
        $approvedThisMonth = BalanceRequest::where('partner_id', $partnerId)
            ->where('status', BalanceRequest::STATUS_APPROVED)
            ->whereMonth('approved_at', now()->month)
            ->whereYear('approved_at', now()->year)
            ->sum('amount');

        return [
            Stat::make(
                label: __('الرصيد الحالي'),
                value: number_format($credit->balance, 2)
            )
                ->description(sprintf(__('الرصيد المتاح: %s'), number_format($credit->available_balance, 2)))
                ->icon('heroicon-o-bank-notes')
                ->color('success')
                ->url(route('filament.customer.resources.balance-history.index', [], false)),

            Stat::make(
                label: __('الطلبات المعلقة'),
                value: (string) $pendingRequestsCount
            )
                ->description(__('طلبات قيد الموافقة'))
                ->icon('heroicon-o-clock')
                ->color($pendingRequestsCount > 0 ? 'warning' : 'success')
                ->url(route('filament.customer.resources.balance-history.index', [], false)),

            Stat::make(
                label: __('المضافة هذا الشهر'),
                value: number_format($approvedThisMonth, 2)
            )
                ->description(sprintf(__('تم إضافتها في %s'), now()->format('F')))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('info'),
        ];
    }
}
