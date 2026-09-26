<?php

declare(strict_types=1);

namespace Webkul\Referral\Services;

use Illuminate\Support\Facades\Schema;
use Webkul\Account\Models\Partner as AccountPartner;
use Webkul\Partner\Models\Partner;
use Webkul\Referral\Models\ReferralCampaign;

class ReferralCodeIssuer
{
    public function __construct(private readonly ReferralService $referrals) {}

    public function issueForActiveCampaigns(?int $companyId = null): int
    {
        if (! $this->isReady()) {
            return 0;
        }

        return ReferralCampaign::query()
            ->active()
            ->when($companyId !== null, fn ($query) => $query->where('company_id', $companyId))
            ->distinct()
            ->pluck('company_id')
            ->sum(fn ($campaignCompanyId): int => $this->issueForCompany((int) $campaignCompanyId));
    }

    public function issueForCompany(int $companyId): int
    {
        if (! $this->isReady()) {
            return 0;
        }

        $created = 0;

        AccountPartner::withoutGlobalScopes()
            ->where('customer_rank', '>', 0)
            ->where(function ($query) use ($companyId): void {
                $query->whereNull('company_id')->orWhere('company_id', $companyId);
            })
            ->orderBy('id')
            ->chunkById(500, function ($customers) use ($companyId, &$created): void {
                foreach ($customers as $customer) {
                    $code = $this->referrals->codeForPartner($customer, $companyId);

                    if ($code->wasRecentlyCreated) {
                        $created++;
                    }
                }
            });

        return $created;
    }

    public function issueForPartner(Partner $partner): int
    {
        if (! $this->isReady() || (int) $partner->customer_rank <= 0) {
            return 0;
        }

        $companyIds = ReferralCampaign::query()
            ->active()
            ->when(
                filled($partner->company_id),
                fn ($query) => $query->where('company_id', (int) $partner->company_id),
            )
            ->distinct()
            ->pluck('company_id')
            ->map(fn ($companyId): int => (int) $companyId);

        return $companyIds->unique()->sum(function (int $companyId) use ($partner): int {
            return $this->referrals->codeForPartner($partner, $companyId)->wasRecentlyCreated ? 1 : 0;
        });
    }

    public function isReady(): bool
    {
        return Schema::hasTable('referral_codes')
            && Schema::hasTable('referral_campaigns')
            && Schema::hasColumn('partners_partners', 'customer_rank');
    }
}
