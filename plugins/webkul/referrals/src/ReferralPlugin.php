<?php

declare(strict_types=1);

namespace Webkul\Referral;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class ReferralPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'referrals';
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel->when($panel->getId() === 'admin', fn (Panel $panel) => $panel
            ->discoverResources(in: __DIR__.'/Filament/Admin/Resources', for: 'Webkul\\Referral\\Filament\\Admin\\Resources'));

        $panel->when($panel->getId() === 'customer', fn (Panel $panel) => $panel
            ->discoverPages(in: __DIR__.'/Filament/Customer/Pages', for: 'Webkul\\Referral\\Filament\\Customer\\Pages'));
    }

    public function boot(Panel $panel): void {}
}
