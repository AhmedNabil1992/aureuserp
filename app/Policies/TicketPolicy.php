<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\Ticket;

class TicketPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->can('view_any_software_ticket');
    }

    public function view(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->can('view_software_ticket');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_ticket');
    }

    public function update(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->can('update_software_ticket');
    }

    public function delete(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->can('delete_software_ticket');
    }

    public function deleteAny(AuthUser $authUser, Ticket $ticket): bool
    {
        return $authUser->can('delete_any_software_ticket');
    }
}
