<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\License;

class LicensePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, License $license): bool
    {
        return $authUser->can('view_any_software_license');
    }

    public function view(AuthUser $authUser, License $license): bool
    {
        return $authUser->can('view_software_license');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_license');
    }

    public function update(AuthUser $authUser, License $license): bool
    {
        return $authUser->can('update_software_license');
    }

    public function delete(AuthUser $authUser, License $license): bool
    {
        return $authUser->can('delete_software_license');
    }

    public function deleteAny(AuthUser $authUser, License $license): bool
    {
        return $authUser->can('delete_any_software_license');
    }
}
