<?php

declare(strict_types=1);

namespace Webkul\Referral\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Webkul\Referral\Jobs\GenerateReferralCodesForCampaign;
use Webkul\Referral\Models\ReferralCampaign;

class ReferralCampaignObserver implements ShouldHandleEventsAfterCommit
{
    public function saved(ReferralCampaign $campaign): void
    {
        if (! $campaign->is_active) {
            return;
        }

        if (! $campaign->wasRecentlyCreated && ! $campaign->wasChanged(['is_active', 'company_id'])) {
            return;
        }

        GenerateReferralCodesForCampaign::dispatchSync($campaign->id);
    }
}
