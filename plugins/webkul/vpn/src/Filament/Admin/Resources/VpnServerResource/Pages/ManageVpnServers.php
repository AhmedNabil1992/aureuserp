<?php

declare(strict_types=1);

namespace Webkul\Vpn\Filament\Admin\Resources\VpnServerResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Vpn\Filament\Admin\Resources\VpnServerResource;

class ManageVpnServers extends ManageRecords
{
    protected static string $resource = VpnServerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label(__('vpn::app.actions.add_server'))->icon('heroicon-o-plus')];
    }
}
