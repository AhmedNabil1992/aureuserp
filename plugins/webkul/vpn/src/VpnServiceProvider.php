<?php

declare(strict_types=1);

namespace Webkul\Vpn;

use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Vpn\Models\VpnServer;
use Webkul\Vpn\Policies\VpnServerPolicy;

class VpnServiceProvider extends PackageServiceProvider
{
    public static string $name = 'vpn';

    public static string $viewNamespace = 'vpn';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations(['2026_09_23_000001_create_vpn_servers_table'])
            ->runsMigrations()
            ->hasInstallCommand(fn (InstallCommand $command) => $command->runsMigrations())
            ->hasUninstallCommand(fn (UninstallCommand $command) => null)
            ->icon('heroicon-o-shield-check');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(VpnPlugin::make()));
    }

    public function packageBooted(): void
    {
        Gate::policy(VpnServer::class, VpnServerPolicy::class);
    }
}
