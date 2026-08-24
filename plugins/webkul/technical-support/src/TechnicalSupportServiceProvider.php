<?php

namespace Webkul\TechnicalSupport;

use Filament\Panel;
use Livewire\Livewire;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\TechnicalSupport\Livewire\OpenTicketsSidebar;
use Webkul\TechnicalSupport\Livewire\TicketConversationPanel;

class TechnicalSupportServiceProvider extends PackageServiceProvider
{
    public static string $name = 'technical-support';

    public static string $viewNamespace = 'technical-support';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasDependencies(['partners'])
            ->hasMigrations([
                '2026_08_24_000001_create_technical_support_tables',
                '2026_08_24_000002_create_quick_downloads_and_canned_replies_tables',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2026_08_24_000003_create_support_auto_reply_settings',
            ])
            ->runsSettings()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {})
            ->icon('heroicon-o-lifebuoy');
    }

    public function packageBooted(): void
    {
        Livewire::component('technical-support-open-tickets-sidebar', OpenTicketsSidebar::class);
        Livewire::component('technical-support-ticket-conversation-panel', TicketConversationPanel::class);

        if (class_exists(\Livewire\Finder\Finder::class)) {
            app(\Livewire\Finder\Finder::class)->addNamespace('technical-support', classNamespace: 'Webkul\\TechnicalSupport\\Livewire');
        }
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(TechnicalSupportPlugin::make());
        });
    }
}
