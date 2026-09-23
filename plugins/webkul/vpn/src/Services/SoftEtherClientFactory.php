<?php

declare(strict_types=1);

namespace Webkul\Vpn\Services;

use Webkul\Vpn\Models\VpnServer;

class SoftEtherClientFactory
{
    public function make(VpnServer $server): SoftEtherClient
    {
        return new SoftEtherClient($server);
    }
}
