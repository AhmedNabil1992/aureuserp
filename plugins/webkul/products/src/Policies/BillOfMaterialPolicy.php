<?php

namespace Webkul\Product\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Product\Models\BillOfMaterial;
use Webkul\Security\Models\User;

class BillOfMaterialPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_product_bill::of::material');
    }

    public function view(User $user, BillOfMaterial $billOfMaterial): bool
    {
        return $user->can('view_product_bill::of::material');
    }

    public function create(User $user): bool
    {
        return $user->can('create_product_bill::of::material');
    }

    public function update(User $user, BillOfMaterial $billOfMaterial): bool
    {
        return $user->can('update_product_bill::of::material');
    }

    public function delete(User $user, BillOfMaterial $billOfMaterial): bool
    {
        return $user->can('delete_product_bill::of::material');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_product_bill::of::material');
    }
}
