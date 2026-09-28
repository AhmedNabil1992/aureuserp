<?php

declare(strict_types=1);

namespace Webkul\Vpn;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class VpnPlugin implements Plugin
{
    public function getId(): string
    {
        return 'vpn';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId()) || $panel->getId() !== 'admin') {
            return;
        }

        $panel
            ->discoverResources(in: __DIR__.'/Filament/Admin/Resources', for: 'Webkul\\Vpn\\Filament\\Admin\\Resources')
            ->discoverPages(in: __DIR__.'/Filament/Admin/Pages', for: 'Webkul\\Vpn\\Filament\\Admin\\Pages');
    }

    public function boot(Panel $panel): void {}
}
