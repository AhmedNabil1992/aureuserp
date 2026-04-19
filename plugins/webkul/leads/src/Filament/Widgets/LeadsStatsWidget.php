<?php

namespace Webkul\Lead\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Lead\Enums\LeadStatus;
use Webkul\Lead\Models\Lead;

class LeadsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                __('leads::filament/widgets/leads-stats.stats.total'),
                Lead::query()->count()
            )->color('gray'),

            Stat::make(
                __('leads::filament/widgets/leads-stats.stats.new'),
                Lead::query()->where('status', LeadStatus::New)->count()
            )->color('info'),

            Stat::make(
                __('leads::filament/widgets/leads-stats.stats.contacted'),
                Lead::query()->where('status', LeadStatus::Contacted)->count()
            )->color('warning'),

            Stat::make(
                __('leads::filament/widgets/leads-stats.stats.qualified'),
                Lead::query()->where('status', LeadStatus::Qualified)->count()
            )->color('primary'),

            Stat::make(
                __('leads::filament/widgets/leads-stats.stats.converted'),
                Lead::query()->where('status', LeadStatus::Converted)->count()
            )->color('success'),

            Stat::make(
                __('leads::filament/widgets/leads-stats.stats.rejected'),
                Lead::query()->where('status', LeadStatus::Rejected)->count()
            )->color('danger'),
        ];
    }
}
