<?php

declare(strict_types=1);

namespace Webkul\Referral\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Referral\Models\ReferralWalletTransaction;
use Webkul\Security\Models\User;

class ReferralWalletTransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->canAny([
            'view_any_referral_referral::wallet::transaction',
            'view_any_referral_referral::redemption',
        ]);
    }

    public function view(User $user, ReferralWalletTransaction $record): bool
    {
        return (int) $record->company_id === (int) current_company_id()
            && $user->canAny([
                'view_referral_referral::wallet::transaction',
                'view_referral_referral::redemption',
            ]);
    }
}
