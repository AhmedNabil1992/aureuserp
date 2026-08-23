<?php

namespace Webkul\TechnicalSupport;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class TechnicalSupportPlugin implements Plugin
{
    public function getId(): string
    {
        return 'technical-support';
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
                        for: 'Webkul\\TechnicalSupport\\Filament\\Admin\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Admin/Pages',
                        for: 'Webkul\\TechnicalSupport\\Filament\\Admin\\Pages'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Admin/Widgets',
                        for: 'Webkul\\TechnicalSupport\\Filament\\Admin\\Widgets'
                    );
            })
            ->when($panel->getId() == 'customer', function (Panel $panel): void {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Customer/Resources',
                        for: 'Webkul\\TechnicalSupport\\Filament\\Customer\\Resources'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
