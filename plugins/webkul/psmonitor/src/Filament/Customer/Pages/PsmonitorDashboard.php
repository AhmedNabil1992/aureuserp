<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Schema;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
class PsmonitorDashboard extends BaseDashboard
{
    use HasPsLicenseAccess;

    protected static string $routePath = 'psmonitor';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/pages/dashboard.navigation.title');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-computer-desktop';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.dashboard');
    }

    public function getWidgets(): array
    {
        return [
            
        ];
    }

}
