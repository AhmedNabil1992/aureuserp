<?php

namespace Webkul\Software;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class SoftwarePlugin implements Plugin
{
    public function getId(): string
    {
        return 'software';
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
                        for: 'Webkul\\Software\\Filament\\Admin\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Admin/Pages',
                        for: 'Webkul\\Software\\Filament\\Admin\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Admin/Clusters',
                        for: 'Webkul\\Software\\Filament\\Admin\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Admin/Widgets',
                        for: 'Webkul\\Software\\Filament\\Admin\\Widgets'
                    );
            })
            ->when($panel->getId() == 'customer', function (Panel $panel): void {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Customer/Clusters',
                        for: 'Webkul\\Software\\Filament\\Customer\\Clusters'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
