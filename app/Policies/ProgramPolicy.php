<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\Program;

class ProgramPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, Program $program): bool
    {
        return $authUser->can('view_any_software_program');
    }

    public function view(AuthUser $authUser, Program $program): bool
    {
        return $authUser->can('view_software_program');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_program');
    }

    public function update(AuthUser $authUser, Program $program): bool
    {
        return $authUser->can('update_software_program');
    }

    public function delete(AuthUser $authUser, Program $program): bool
    {
        return $authUser->can('delete_software_program');
    }

    public function deleteAny(AuthUser $authUser, Program $program): bool
    {
        return $authUser->can('delete_any_software_program');
    }
}
