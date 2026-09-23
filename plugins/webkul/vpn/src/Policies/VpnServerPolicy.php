<?php

declare(strict_types=1);

namespace Webkul\Vpn\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Vpn\Models\VpnServer;

class VpnServerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_vpn_server');
    }

    public function view(User $user, VpnServer $server): bool
    {
        return $user->can('view_vpn_server');
    }

    public function create(User $user): bool
    {
        return $user->can('create_vpn_server');
    }

    public function update(User $user, VpnServer $server): bool
    {
        return $user->can('update_vpn_server');
    }

    public function delete(User $user, VpnServer $server): bool
    {
        return $user->can('delete_vpn_server');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_vpn_server');
    }
}
