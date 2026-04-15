<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\ProgramFeature;

class ProgramFeaturePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, ProgramFeature $programFeature): bool
    {
        return $authUser->can('view_any_software_program::feature');
    }

    public function view(AuthUser $authUser, ProgramFeature $programFeature): bool
    {
        return $authUser->can('view_software_program::feature');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_program::feature');
    }

    public function update(AuthUser $authUser, ProgramFeature $programFeature): bool
    {
        return $authUser->can('update_software_program::feature');
    }

    public function delete(AuthUser $authUser, ProgramFeature $programFeature): bool
    {
        return $authUser->can('delete_software_program::feature');
    }

    public function deleteAny(AuthUser $authUser, ProgramFeature $programFeature): bool
    {
        return $authUser->can('delete_any_software_program::feature');
    }
}
