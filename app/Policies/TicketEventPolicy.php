<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\Software\Models\TicketEvent;

class TicketEventPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser, TicketEvent $ticketEvent): bool
    {
        return $authUser->can('view_any_software_ticket::event');
    }

    public function view(AuthUser $authUser, TicketEvent $ticketEvent): bool
    {
        return $authUser->can('view_software_ticket::event');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_software_ticket::event');
    }

    public function update(AuthUser $authUser, TicketEvent $ticketEvent): bool
    {
        return $authUser->can('update_software_ticket::event');
    }

    public function delete(AuthUser $authUser, TicketEvent $ticketEvent): bool
    {
        return $authUser->can('delete_software_ticket::event');
    }

    public function deleteAny(AuthUser $authUser, TicketEvent $ticketEvent): bool
    {
        return $authUser->can('delete_any_software_ticket::event');
    }
}
