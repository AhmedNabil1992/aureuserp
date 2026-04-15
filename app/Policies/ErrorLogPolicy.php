<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\ErrorLog;

class ErrorLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, ErrorLog $errorLog): bool
    {
        return $authUser->can('view_any_software_error::log');
    }

    public function view(AuthUser $authUser, ErrorLog $errorLog): bool
    {
        return $authUser->can('view_software_error::log');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_error::log');
    }

    public function update(AuthUser $authUser, ErrorLog $errorLog): bool
    {
        return $authUser->can('update_software_error::log');
    }

    public function delete(AuthUser $authUser, ErrorLog $errorLog): bool
    {
        return $authUser->can('delete_software_error::log');
    }

    public function deleteAny(AuthUser $authUser, ErrorLog $errorLog): bool
    {
        return $authUser->can('delete_any_software_error::log');
    }
}
