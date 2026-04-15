<?php

namespace Webkul\Software\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Software\Enums\TicketStatus;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseDevice;
use Webkul\Software\Models\Program;
use Webkul\Software\Models\Ticket;

class SoftwareStatsOverviewWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '15s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_software_stats_overview_widget';
    }

    protected function getHeading(): ?string
    {
        return __('software::filament/admin/widgets/dashboard.stats.heading');
    }

    protected function getStats(): array
    {
        $programsCount = $this->applyCreatedAtFilters(Program::query())->count();

        $activeLicensesCount = $this->applyCreatedAtFilters(License::query())
            ->where('is_active', true)
            ->count();

        $devicesCount = $this->applyCreatedAtFilters(LicenseDevice::query())->count();

        $openTicketsCount = $this->applyCreatedAtFilters(Ticket::query())
            ->where('status', TicketStatus::Open->value)
            ->count();

        return [
            Stat::make(__('software::filament/admin/widgets/dashboard.stats.programs.label'), $programsCount)
                ->description(__('software::filament/admin/widgets/dashboard.stats.programs.description')),

            Stat::make(__('software::filament/admin/widgets/dashboard.stats.active_licenses.label'), $activeLicensesCount)
                ->description(__('software::filament/admin/widgets/dashboard.stats.active_licenses.description')),

            Stat::make(__('software::filament/admin/widgets/dashboard.stats.registered_devices.label'), $devicesCount)
                ->description(__('software::filament/admin/widgets/dashboard.stats.registered_devices.description')),

            Stat::make(__('software::filament/admin/widgets/dashboard.stats.open_tickets.label'), $openTicketsCount)
                ->description(__('software::filament/admin/widgets/dashboard.stats.open_tickets.description')),
        ];
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
