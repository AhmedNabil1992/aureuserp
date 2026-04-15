<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Product\Models\BillOfMaterial;

class BillOfMaterialPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, BillOfMaterial $billOfMaterial): bool
    {
        return $authUser->can('view_any_product_bill::of::material');
    }

    public function view(AuthUser $authUser, BillOfMaterial $billOfMaterial): bool
    {
        return $authUser->can('view_product_bill::of::material');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_product_bill::of::material');
    }

    public function update(AuthUser $authUser, BillOfMaterial $billOfMaterial): bool
    {
        return $authUser->can('update_product_bill::of::material');
    }

    public function delete(AuthUser $authUser, BillOfMaterial $billOfMaterial): bool
    {
        return $authUser->can('delete_product_bill::of::material');
    }

    public function deleteAny(AuthUser $authUser, BillOfMaterial $billOfMaterial): bool
    {
        return $authUser->can('delete_any_product_bill::of::material');
    }
}
