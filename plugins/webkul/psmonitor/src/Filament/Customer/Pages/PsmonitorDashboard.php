<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Widgets\ActiveDevicesOverviewWidget;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Filament\Customer\Widgets\TrxOpenShiftTotalsWidget;
use Webkul\Psmonitor\Filament\Customer\Widgets\TrxTypeTotalsWidget;

class PsmonitorDashboard extends BaseDashboard
{
    use HasPsLicenseAccess;

    protected static string $routePath = 'psmonitor';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/dashboard.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/dashboard.title');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-computer-desktop';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.dashboard');
    }

    public function getHeaderWidgets(): array
    {
        return [
            LicenseSelectorWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            ActiveDevicesOverviewWidget::class,
            TrxOpenShiftTotalsWidget::class,
            TrxTypeTotalsWidget::class,
        ];
    }
}
