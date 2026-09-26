<?php

declare(strict_types=1);

namespace Webkul\Referral\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Referral\Models\ReferralRedemption;
use Webkul\Security\Models\User;

class ReferralRedemptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_referral_referral::redemption');
    }

    public function view(User $user, ReferralRedemption $record): bool
    {
        return (int) $record->company_id === (int) current_company_id()
            && $user->can('view_referral_referral::redemption');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ReferralRedemption $record): bool
    {
        return false;
    }

    public function delete(User $user, ReferralRedemption $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
