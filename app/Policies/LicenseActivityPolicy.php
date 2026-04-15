<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\LicenseActivity;

class LicenseActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, LicenseActivity $licenseActivity): bool
    {
        return $authUser->can('view_any_software_license::activity');
    }

    public function view(AuthUser $authUser, LicenseActivity $licenseActivity): bool
    {
        return $authUser->can('view_software_license::activity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_license::activity');
    }

    public function update(AuthUser $authUser, LicenseActivity $licenseActivity): bool
    {
        return $authUser->can('update_software_license::activity');
    }

    public function delete(AuthUser $authUser, LicenseActivity $licenseActivity): bool
    {
        return $authUser->can('delete_software_license::activity');
    }

    public function deleteAny(AuthUser $authUser, LicenseActivity $licenseActivity): bool
    {
        return $authUser->can('delete_any_software_license::activity');
    }
}
