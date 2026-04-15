<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\RemoteProfile;

class RemoteProfilePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, RemoteProfile $remoteProfile): bool
    {
        return $authUser->can('view_any_software_remote::profile');
    }

    public function view(AuthUser $authUser, RemoteProfile $remoteProfile): bool
    {
        return $authUser->can('view_software_remote::profile');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_remote::profile');
    }

    public function update(AuthUser $authUser, RemoteProfile $remoteProfile): bool
    {
        return $authUser->can('update_software_remote::profile');
    }

    public function delete(AuthUser $authUser, RemoteProfile $remoteProfile): bool
    {
        return $authUser->can('delete_software_remote::profile');
    }

    public function deleteAny(AuthUser $authUser, RemoteProfile $remoteProfile): bool
    {
        return $authUser->can('delete_any_software_remote::profile');
    }
}
