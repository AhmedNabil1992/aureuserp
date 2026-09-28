<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Filament\Admin\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\AttentionSubscriptionsWidget;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\InstanceStatusChart;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\OnlineStatsOverview;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\RecentSyncsWidget;
use Webkul\SoftwareOnline\Filament\Admin\Widgets\SubscriptionRevenueChart;
use Webkul\Support\Enums\NavigationGroup;

class OnlineDashboard extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = 'software-online';

    protected static ?int $navigationSort = 0;

    protected static function getPagePermission(): ?string
    {
        return 'page_software_online_online_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('software-online::filament/admin/pages/dashboard.navigation.label');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::SoftwareOnline;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-squares-2x2';
    }

    public function getTitle(): string|Htmlable
    {
        return __('software-online::filament/admin/pages/dashboard.title');
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'xl'      => 3,
        ];
    }

    public function getWidgets(): array
    {
        return [
            OnlineStatsOverview::class,
            SubscriptionRevenueChart::class,
            InstanceStatusChart::class,
            AttentionSubscriptionsWidget::class,
            RecentSyncsWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createSubscription')
                ->label(__('software-online::filament/admin/pages/dashboard.actions.create_subscription'))
                ->icon('heroicon-o-plus')
                ->url(OnlineInstanceResource::getUrl('create')),
        ];
    }
}
