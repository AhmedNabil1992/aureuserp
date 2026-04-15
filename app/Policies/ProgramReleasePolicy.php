<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\ProgramRelease;

class ProgramReleasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, ProgramRelease $programRelease): bool
    {
        return $authUser->can('view_any_software_program::release');
    }

    public function view(AuthUser $authUser, ProgramRelease $programRelease): bool
    {
        return $authUser->can('view_software_program::release');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_program::release');
    }

    public function update(AuthUser $authUser, ProgramRelease $programRelease): bool
    {
        return $authUser->can('update_software_program::release');
    }

    public function delete(AuthUser $authUser, ProgramRelease $programRelease): bool
    {
        return $authUser->can('delete_software_program::release');
    }

    public function deleteAny(AuthUser $authUser, ProgramRelease $programRelease): bool
    {
        return $authUser->can('delete_any_software_program::release');
    }
}
