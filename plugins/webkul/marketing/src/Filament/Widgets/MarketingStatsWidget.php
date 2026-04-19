<?php

namespace Webkul\Marketing\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Lead\Models\Lead;
use Webkul\Marketing\Enums\CampaignStatus;
use Webkul\Marketing\Models\Campaign;

class MarketingStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalLeads = Lead::query()->count();
        $leadsFromCampaigns = Lead::query()->whereNotNull('campaign_id')->count();
        $activeCampaigns = Campaign::query()->where('status', CampaignStatus::Active)->count();
        $completedCampaigns = Campaign::query()->where('status', CampaignStatus::Completed)->count();

        return [
            Stat::make(
                __('marketing::filament/widgets/marketing-stats.stats.total-campaigns'),
                Campaign::query()->count()
            )
                ->description(__('marketing::filament/widgets/marketing-stats.descriptions.all-time'))
                ->color('gray'),

            Stat::make(
                __('marketing::filament/widgets/marketing-stats.stats.active-campaigns'),
                $activeCampaigns
            )
                ->description(__('marketing::filament/widgets/marketing-stats.descriptions.currently-running'))
                ->color('success'),

            Stat::make(
                __('marketing::filament/widgets/marketing-stats.stats.completed-campaigns'),
                $completedCampaigns
            )
                ->description(__('marketing::filament/widgets/marketing-stats.descriptions.finished-campaigns'))
                ->color('info'),

            Stat::make(
                __('marketing::filament/widgets/marketing-stats.stats.total-leads'),
                $totalLeads
            )
                ->description(__('marketing::filament/widgets/marketing-stats.descriptions.across-all'))
                ->color('primary'),

            Stat::make(
                __('marketing::filament/widgets/marketing-stats.stats.leads-from-campaigns'),
                $leadsFromCampaigns
            )
                ->description(
                    $totalLeads > 0
                        ? __('marketing::filament/widgets/marketing-stats.descriptions.attribution-rate', ['rate' => round(($leadsFromCampaigns / $totalLeads) * 100, 1)])
                        : __('marketing::filament/widgets/marketing-stats.descriptions.no-leads')
                )
                ->color('warning'),
        ];
    }
}
