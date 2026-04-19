<?php

namespace Webkul\Marketing;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class MarketingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'marketing';

    public static string $viewNamespace = 'marketing';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2026_04_19_000001_create_marketing_campaigns_table',
                '2026_04_19_000002_create_marketing_ad_plans_table',
            ])
            ->runsMigrations()
            ->hasSettings([])
            ->runsSettings()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('heroicon-o-megaphone');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(MarketingPlugin::make());
        });
    }
}
