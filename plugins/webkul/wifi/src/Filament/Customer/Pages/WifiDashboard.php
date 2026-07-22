<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Facades\Filament;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Illuminate\Support\Facades\Schema;
use Webkul\Wifi\Filament\Customer\Widgets\QoutaUsage;

class WifiDashboard extends BaseDashboard
{
    protected static string $routePath = 'wifi';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        if (! Schema::hasTable('wifi_partner_clouds')) {
            return false;
        }

        return WifiPartnerCloud::where('partner_id', $user->id)->exists();
    }

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/pages/dashboard.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.dashboard');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-wifi';
    }

    public function getWidgets(): array
    {
        return [
            QoutaUsage::class,

        ];
    }

}
