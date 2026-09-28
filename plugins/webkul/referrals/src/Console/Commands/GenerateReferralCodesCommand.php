<?php

declare(strict_types=1);

namespace Webkul\Referral\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Referral\Services\ReferralCodeIssuer;

class GenerateReferralCodesCommand extends Command
{
    protected $signature = 'referrals:generate-codes {--company= : Generate codes for one company only}';

    protected $description = 'Generate missing referral codes for customers covered by active campaigns';

    public function handle(ReferralCodeIssuer $issuer): int
    {
        if (! $issuer->isReady()) {
            $this->error('Referral migrations are not installed on the current database connection.');

            return self::FAILURE;
        }

        $companyId = filled($this->option('company')) ? (int) $this->option('company') : null;
        $created = $issuer->issueForActiveCampaigns($companyId);

        $this->info("Generated {$created} missing referral code(s).");

        return self::SUCCESS;
    }
}
