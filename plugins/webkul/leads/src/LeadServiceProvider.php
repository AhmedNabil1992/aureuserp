<?php

namespace Webkul\Lead;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class LeadServiceProvider extends PackageServiceProvider
{
    public static string $name = 'leads';

    public static string $viewNamespace = 'leads';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2026_04_19_000001_create_leads_leads_table',
                '2026_04_19_000002_create_leads_interactions_table',
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
            ->icon('heroicon-o-user-group');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(LeadPlugin::make());
        });
    }
}
