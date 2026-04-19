<?php

namespace Webkul\Marketing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Marketing\Models\Campaign;
use Webkul\Security\Models\User;

class CampaignPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_marketing_campaign');
    }

    public function view(User $user, Campaign $campaign): bool
    {
        return $user->can('view_marketing_campaign');
    }

    public function create(User $user): bool
    {
        return $user->can('create_marketing_campaign');
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return $user->can('update_marketing_campaign');
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        return $user->can('delete_marketing_campaign');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_marketing_campaign');
    }

    public function forceDelete(User $user, Campaign $campaign): bool
    {
        return $user->can('force_delete_marketing_campaign');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_marketing_campaign');
    }

    public function restore(User $user, Campaign $campaign): bool
    {
        return $user->can('restore_marketing_campaign');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_marketing_campaign');
    }
}
