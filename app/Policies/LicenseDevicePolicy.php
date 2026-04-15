<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\LicenseDevice;

class LicenseDevicePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, LicenseDevice $licenseDevice): bool
    {
        return $authUser->can('view_any_software_license::device');
    }

    public function view(AuthUser $authUser, LicenseDevice $licenseDevice): bool
    {
        return $authUser->can('view_software_license::device');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_license::device');
    }

    public function update(AuthUser $authUser, LicenseDevice $licenseDevice): bool
    {
        return $authUser->can('update_software_license::device');
    }

    public function delete(AuthUser $authUser, LicenseDevice $licenseDevice): bool
    {
        return $authUser->can('delete_software_license::device');
    }

    public function deleteAny(AuthUser $authUser, LicenseDevice $licenseDevice): bool
    {
        return $authUser->can('delete_any_software_license::device');
    }
}
