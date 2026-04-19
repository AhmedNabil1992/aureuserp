<?php

namespace Webkul\Wifi;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class WifiPlugin implements Plugin
{
    public function getId(): string
    {
        return 'wifi';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel
            ->when($panel->getId() == 'admin', function (Panel $panel): void {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Admin/Resources',
                        for: 'Webkul\\Wifi\\Filament\\Admin\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Admin/Pages',
                        for: 'Webkul\\Wifi\\Filament\\Admin\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Admin/Clusters',
                        for: 'Webkul\\Wifi\\Filament\\Admin\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Admin/Widgets',
                        for: 'Webkul\\Wifi\\Filament\\Admin\\Widgets'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
