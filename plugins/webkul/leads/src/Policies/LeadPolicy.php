<?php

namespace Webkul\Lead\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Lead\Models\Lead;
use Webkul\Security\Models\User;

class LeadPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_lead_lead');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can('view_lead_lead');
    }

    public function create(User $user): bool
    {
        return $user->can('create_lead_lead');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->can('update_lead_lead');
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('delete_lead_lead');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_lead_lead');
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->can('force_delete_lead_lead');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_lead_lead');
    }

    public function restore(User $user, Lead $lead): bool
    {
        return $user->can('restore_lead_lead');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_lead_lead');
    }
}
