<?php

namespace Webkul\Lead\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Lead\Models\Interaction;
use Webkul\Security\Models\User;

class InteractionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_lead_interaction');
    }

    public function view(User $user, Interaction $interaction): bool
    {
        return $user->can('view_lead_interaction');
    }

    public function create(User $user): bool
    {
        return $user->can('create_lead_interaction');
    }

    public function update(User $user, Interaction $interaction): bool
    {
        return $user->can('update_lead_interaction');
    }

    public function delete(User $user, Interaction $interaction): bool
    {
        return $user->can('delete_lead_interaction');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_lead_interaction');
    }
}
