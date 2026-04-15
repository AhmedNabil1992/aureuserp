<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\LicenseSubscription;

class LicenseSubscriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, LicenseSubscription $licenseSubscription): bool
    {
        return $authUser->can('view_any_software_license::subscription');
    }

    public function view(AuthUser $authUser, LicenseSubscription $licenseSubscription): bool
    {
        return $authUser->can('view_software_license::subscription');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_license::subscription');
    }

    public function update(AuthUser $authUser, LicenseSubscription $licenseSubscription): bool
    {
        return $authUser->can('update_software_license::subscription');
    }

    public function delete(AuthUser $authUser, LicenseSubscription $licenseSubscription): bool
    {
        return $authUser->can('delete_software_license::subscription');
    }

    public function deleteAny(AuthUser $authUser, LicenseSubscription $licenseSubscription): bool
    {
        return $authUser->can('delete_any_software_license::subscription');
    }
}
