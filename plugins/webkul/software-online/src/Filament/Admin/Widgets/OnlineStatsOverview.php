<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineInstanceTransaction;

class OnlineStatsOverview extends StatsOverviewWidget
{
    use HasWidgetShield;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_online_online_stats_overview';
    }

    protected function getStats(): array
    {
        $instances = $this->instancesQuery();
        $total = (clone $instances)->count();
        $active = (clone $instances)->where('status', InstanceStatus::Active->value)->count();
        $awaiting = (clone $instances)->whereIn('status', [
            InstanceStatus::Pending->value,
            InstanceStatus::Provisioning->value,
        ])->count();
        $failed = (clone $instances)->where('status', InstanceStatus::Failed->value)->count();

        $revenue = OnlineInstanceTransaction::query()
            ->where('status', 'paid')
            ->whereHas('partner', fn (Builder $query): Builder => $this->scopeToCompany($query))
            ->sum('amount');

        return [
            Stat::make(__('software-online::filament/admin/widgets/dashboard.stats.total.label'), number_format($total))
                ->description(__('software-online::filament/admin/widgets/dashboard.stats.total.description'))
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary'),
            Stat::make(__('software-online::filament/admin/widgets/dashboard.stats.active.label'), number_format($active))
                ->description(__('software-online::filament/admin/widgets/dashboard.stats.active.description', [
                    'percentage' => $total > 0 ? round(($active / $total) * 100) : 0,
                ]))
                ->descriptionIcon('heroicon-m-bolt')
                ->color('success'),
            Stat::make(__('software-online::filament/admin/widgets/dashboard.stats.awaiting.label'), number_format($awaiting))
                ->description(__('software-online::filament/admin/widgets/dashboard.stats.awaiting.description', ['failed' => $failed]))
                ->descriptionIcon('heroicon-m-clock')
                ->color($failed > 0 ? 'danger' : 'warning'),
            Stat::make(__('software-online::filament/admin/widgets/dashboard.stats.revenue.label'), number_format((float) $revenue, 2).' EGP')
                ->description(__('software-online::filament/admin/widgets/dashboard.stats.revenue.description'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }

    private function instancesQuery(): Builder
    {
        return OnlineInstance::query()
            ->whereHas('partner', fn (Builder $query): Builder => $this->scopeToCompany($query));
    }

    private function scopeToCompany(Builder $query): Builder
    {
        return $query->where(owned_by_company());
    }
}
