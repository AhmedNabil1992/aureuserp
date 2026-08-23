<?php

namespace Webkul\SoftwareOnline;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class SoftwareOnlineServiceProvider extends PackageServiceProvider
{
    public static string $name = 'software-online';

    public static string $viewNamespace = 'software-online';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasDependencies(['partners', 'accounts'])
            ->hasMigrations([
                '2026_08_24_000001_create_online_systems_tables',
                '2026_08_24_000002_add_product_and_invoicing_to_online_systems_tables',
            ])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->askToStarRepoOnGitHub('webkul/software-online');
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(SoftwareOnlinePlugin::make());
        });
    }
}
