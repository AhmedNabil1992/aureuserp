<?php

declare(strict_types=1);

namespace Webkul\TechnicalSupport\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Webkul\TechnicalSupport\Models\Ticket;

class TicketPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function view(AuthUser $authUser, Ticket $ticket): bool
    {
        return true;
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, Ticket $ticket): bool
    {
        return true;
    }

    public function delete(AuthUser $authUser, Ticket $ticket): bool
    {
        return true;
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return true;
    }
}
