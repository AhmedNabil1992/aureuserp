<?php

namespace Webkul\Psmonitor;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class PsmonitorPlugin implements Plugin
{
    public function getId(): string
    {
        return 'psmonitor';
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
            // ->when($panel->getId() == 'admin', function (Panel $panel): void {
            //     $panel
            //         ->discoverResources(
            //             in: __DIR__.'/Filament/Admin/Resources',
            //             for: 'Webkul\\PSMonitor\\Filament\\Admin\\Resources'
            //         )
            //         ->discoverPages(
            //             in: __DIR__.'/Filament/Admin/Pages',
            //             for: 'Webkul\\PSMonitor\\Filament\\Admin\\Pages'
            //         )
            //         ->discoverClusters(
            //             in: __DIR__.'/Filament/Admin/Clusters',
            //             for: 'Webkul\\PSMonitor\\Filament\\Admin\\Clusters'
            //         )
            //         ->discoverWidgets(
            //             in: __DIR__.'/Filament/Admin/Widgets',
            //             for: 'Webkul\\PSMonitor\\Filament\\Admin\\Widgets'
            //         );
            // })
            ->when($panel->getId() == 'customer', function (Panel $panel): void {
                $panel
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Customer/Clusters',
                        for: 'Webkul\\Psmonitor\\Filament\\Customer\\Clusters'
                    )
                    ->discoverResources(
                        in: __DIR__.'/Filament/Customer/Resources',
                        for: 'Webkul\\Psmonitor\\Filament\\Customer\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Customer/Pages',
                        for: 'Webkul\\Psmonitor\\Filament\\Customer\\Pages'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Customer/Widgets',
                        for: 'Webkul\\Psmonitor\\Filament\\Customer\\Widgets'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}