<?php

namespace Webkul\Wifi;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class WifiServiceProvider extends PackageServiceProvider
{
    public static string $name = 'wifi';

    public static string $viewNamespace = 'wifi';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasDependencies(['accounts', 'partners'])
            ->hasMigrations([
                '2026_04_19_200001_create_wifi_packages_table',
                '2026_04_19_200002_create_wifi_purchases_table',
                '2026_04_19_200003_create_wifi_voucher_batches_table',
                '2026_04_20_000101_create_wifi_partner_clouds_table',
                '2026_04_20_000201_alter_wifi_packages_add_currency_id',
            ])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {})
            ->icon('heroicon-o-wifi');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(WifiPlugin::make());
        });
    }
}
