<?php

namespace Webkul\Psmonitor;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class PsmonitorServiceProvider extends PackageServiceProvider
{
    public static string $name = 'psmonitor';

    public static string $viewNamespace = 'psmonitor';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasRoutes(['web', 'api'])
            ->hasDependencies(['software', 'accounts', 'partners'])
            // ->hasMigrations([
                
            // ])
            // ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {})
            ->icon('heroicon-o-computer-desktop');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(PsmonitorPlugin::make());
        });
    }
}