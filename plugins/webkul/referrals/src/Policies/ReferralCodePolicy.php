<?php

declare(strict_types=1);

namespace Webkul\Referral\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Referral\Models\ReferralCode;
use Webkul\Security\Models\User;

class ReferralCodePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_referral_referral::code');
    }

    public function view(User $user, ReferralCode $record): bool
    {
        return (int) $record->company_id === (int) current_company_id()
            && $user->can('view_referral_referral::code');
    }

    public function create(User $user): bool
    {
        return $user->can('create_referral_referral::code');
    }

    public function update(User $user, ReferralCode $record): bool
    {
        return (int) $record->company_id === (int) current_company_id()
            && $user->can('update_referral_referral::code');
    }

    public function delete(User $user, ReferralCode $record): bool
    {
        return (int) $record->company_id === (int) current_company_id()
            && $user->can('delete_referral_referral::code');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_referral_referral::code');
    }
}
