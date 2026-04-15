<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Support\Models\City;

class CityPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, City $city): bool
    {
        return $authUser->can('view_any_support_city');
    }

    public function view(AuthUser $authUser, City $city): bool
    {
        return $authUser->can('view_support_city');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_support_city');
    }

    public function update(AuthUser $authUser, City $city): bool
    {
        return $authUser->can('update_support_city');
    }

    public function delete(AuthUser $authUser, City $city): bool
    {
        return $authUser->can('delete_support_city');
    }

    public function deleteAny(AuthUser $authUser, City $city): bool
    {
        return $authUser->can('delete_any_support_city');
    }
}
