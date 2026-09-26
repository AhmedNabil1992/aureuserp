<?php

declare(strict_types=1);

namespace Webkul\Referral\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Webkul\Referral\Models\ReferralCampaign;
use Webkul\Referral\Services\ReferralCodeIssuer;

class GenerateReferralCodesForCampaign implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $campaignId) {}

    public function handle(ReferralCodeIssuer $issuer): void
    {
        $campaign = ReferralCampaign::query()->find($this->campaignId);

        if (! $campaign?->is_active) {
            return;
        }

        $issuer->issueForCompany((int) $campaign->company_id);
    }
}
