<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Inventory\Models\Lot;

class LotPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, Lot $lot): bool
    {
        return $authUser->can('view_any_inventory_lot');
    }

    public function view(AuthUser $authUser, Lot $lot): bool
    {
        return $authUser->can('view_inventory_lot');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_inventory_lot');
    }

    public function update(AuthUser $authUser, Lot $lot): bool
    {
        return $authUser->can('update_inventory_lot');
    }

    public function delete(AuthUser $authUser, Lot $lot): bool
    {
        return $authUser->can('delete_inventory_lot');
    }

    public function deleteAny(AuthUser $authUser, Lot $lot): bool
    {
        return $authUser->can('delete_any_inventory_lot');
    }
}
