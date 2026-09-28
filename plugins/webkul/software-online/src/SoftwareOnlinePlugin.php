<?php

namespace Webkul\SoftwareOnline;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;
use Webkul\SoftwareOnline\Filament\Admin\Pages\OnlineDashboard;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource as AdminOnlineInstanceResource;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlinePlanResource;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineSystemResource;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineTransactionResource;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\AttentionSubscriptionsWidget;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\InstanceStatusChart;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\OnlineStatsOverview;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\RecentSyncsWidget;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\SubscriptionRevenueChart;
use Webkul\SoftwareOnline\Filament\Customer\Pages\ExploreSystemsPage;
use Webkul\SoftwareOnline\Filament\Customer\Resources\OnlineInstanceResource as CustomerOnlineInstanceResource;

class SoftwareOnlinePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'software-online';
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        if ($panel->getId() === 'admin') {
            $panel
                ->pages([
                    OnlineDashboard::class,
                ])
                ->resources([
                    OnlineSystemResource::class,
                    OnlinePlanResource::class,
                    AdminOnlineInstanceResource::class,
                    OnlineTransactionResource::class,
                ])
                ->widgets([
                    OnlineStatsOverview::class,
                    SubscriptionRevenueChart::class,
                    InstanceStatusChart::class,
                    AttentionSubscriptionsWidget::class,
                    RecentSyncsWidget::class,
                ]);
        } elseif ($panel->getId() === 'customer') {
            $panel
                ->pages([
                    ExploreSystemsPage::class,
                ])
                ->resources([
                    CustomerOnlineInstanceResource::class,
                ]);
        }
    }

    public function boot(Panel $panel): void {}
}
