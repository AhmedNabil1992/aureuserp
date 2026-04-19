<?php

namespace Webkul\Article;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class ArticlePlugin implements Plugin
{
    public function getId(): string
    {
        return 'articles';
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
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Admin/Resources',
                        for: 'Webkul\\Article\\Filament\\Admin\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Admin/Pages',
                        for: 'Webkul\\Article\\Filament\\Admin\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Admin/Clusters',
                        for: 'Webkul\\Article\\Filament\\Admin\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Admin/Widgets',
                        for: 'Webkul\\Article\\Filament\\Admin\\Widgets'
                    );
            })
            ->when($panel->getId() == 'customer', function (Panel $panel) {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Customer/Resources',
                        for: 'Webkul\\Article\\Filament\\Customer\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Customer/Pages',
                        for: 'Webkul\\Article\\Filament\\Customer\\Pages'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
