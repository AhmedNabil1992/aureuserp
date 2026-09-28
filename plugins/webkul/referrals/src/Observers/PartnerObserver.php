<?php

declare(strict_types=1);

namespace Webkul\Referral\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Webkul\Partner\Models\Partner;
use Webkul\Referral\Services\ReferralCodeIssuer;

class PartnerObserver implements ShouldHandleEventsAfterCommit
{
    public function created(Partner $partner): void
    {
        app(ReferralCodeIssuer::class)->issueForPartner($partner);
    }

    public function updated(Partner $partner): void
    {
        if ($partner->wasChanged(['customer_rank', 'company_id'])) {
            app(ReferralCodeIssuer::class)->issueForPartner($partner);
        }
    }
}
