<?php

namespace Webkul\Wifi\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Webkul\Wifi\Models\Radacct;

class QoutaUsage extends StatsOverviewWidget
{
    protected ?string $heading = 'استخدام الإنترنت الأسبوعي (جيجابايت)';

    protected function getStats(): array
    {
        return [
            Stat::make('used', 'استخدام الإنترنت')
                ->description('استخدام الإنترنت الأسبوعي')
                ->descriptionIcon('heroicon-o-arrow-up')
                ,
        ];
    }
}
