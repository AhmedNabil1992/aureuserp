<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\ProgramEdition;

class ProgramEditionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, ProgramEdition $programEdition): bool
    {
        return $authUser->can('view_any_software_program::edition');
    }

    public function view(AuthUser $authUser, ProgramEdition $programEdition): bool
    {
        return $authUser->can('view_software_program::edition');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_program::edition');
    }

    public function update(AuthUser $authUser, ProgramEdition $programEdition): bool
    {
        return $authUser->can('update_software_program::edition');
    }

    public function delete(AuthUser $authUser, ProgramEdition $programEdition): bool
    {
        return $authUser->can('delete_software_program::edition');
    }

    public function deleteAny(AuthUser $authUser, ProgramEdition $programEdition): bool
    {
        return $authUser->can('delete_any_software_program::edition');
    }
}
