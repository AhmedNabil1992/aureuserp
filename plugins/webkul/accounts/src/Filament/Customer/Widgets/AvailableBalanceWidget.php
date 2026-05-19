<?php

namespace Webkul\Account\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\MoveLine;

class AvailableBalanceWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $partner = Auth::guard('customer')->user();

        if (! $partner) {
            return [];
        }

        $availableBalance = (float) MoveLine::query()
            ->where('partner_id', $partner->id)
            ->where('parent_state', MoveState::POSTED)
            ->where('reconciled', false)
            ->where('balance', '<', 0)
            ->where('amount_residual', '<', 0)
            ->whereHas('account', fn ($query) => $query->where('account_type', AccountType::ASSET_RECEIVABLE))
            ->sum('amount_residual');

        $currencyCode = $partner->company?->currency?->code ?? config('app.currency', 'EGP');

        return [
            Stat::make(
                __('accounts::filament/customer/widgets/available-balance-widget.stats.available-balance'),
                money(abs($availableBalance), $currencyCode)
            )
                ->description(__('accounts::filament/customer/widgets/available-balance-widget.stats.available-balance-description'))
                ->color(abs($availableBalance) > 0 ? 'success' : 'gray'),
        ];
    }
}
