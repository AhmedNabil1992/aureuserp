<?php

namespace Webkul\SoftwareOnline;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource as AdminOnlineInstanceResource;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlinePlanResource;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineSystemResource;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineTransactionResource;
use Webkul\SoftwareOnline\Filament\Customer\Pages\ExploreSystemsPage;
use Webkul\SoftwareOnline\Filament\Customer\Resources\OnlineInstanceResource as CustomerOnlineInstanceResource;

class SoftwareOnlinePlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'software-online';
    }

    public function register(Panel $panel): void
    {
        if ($panel->getId() === 'admin') {
            $panel->resources([
                OnlineSystemResource::class,
                OnlinePlanResource::class,
                AdminOnlineInstanceResource::class,
                OnlineTransactionResource::class,
            ]);
        } elseif ($panel->getId() === 'customer') {
            $panel
                ->pages([
                    ExploreSystemsPage::class,
                ])
                ->resources([
                    CustomerOnlineInstanceResource::class,
                ]);
        }
    }

    public function boot(Panel $panel): void {}
}
