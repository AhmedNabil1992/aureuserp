<?php

declare(strict_types=1);

namespace Webkul\Referral\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Referral\Models\ReferralCampaign;
use Webkul\Security\Models\User;

class ReferralCampaignPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_referral_referral::campaign');
    }

    public function view(User $user, ReferralCampaign $record): bool
    {
        return (int) $record->company_id === (int) current_company_id()
            && $user->can('view_referral_referral::campaign');
    }

    public function create(User $user): bool
    {
        return $user->can('create_referral_referral::campaign');
    }

    public function update(User $user, ReferralCampaign $record): bool
    {
        return (int) $record->company_id === (int) current_company_id()
            && $user->can('update_referral_referral::campaign');
    }

    public function delete(User $user, ReferralCampaign $record): bool
    {
        return (int) $record->company_id === (int) current_company_id()
            && $user->can('delete_referral_referral::campaign');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_referral_referral::campaign');
    }
}
