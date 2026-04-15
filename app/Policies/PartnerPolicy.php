<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PartnerPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_website_partner');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('view_website_partner');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_website_partner');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('update_website_partner');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('delete_website_partner');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_website_partner');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('restore_website_partner');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_website_partner');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_website_partner');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_website_partner');
    }
}
