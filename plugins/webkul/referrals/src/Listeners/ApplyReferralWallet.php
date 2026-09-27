<?php

declare(strict_types=1);

namespace Webkul\Referral\Listeners;

use Webkul\Account\Events\MoveConfirmed;
use Webkul\Referral\Services\ReferralWalletService;

class ApplyReferralWallet
{
    public function handle(MoveConfirmed $event): void
    {
        app(ReferralWalletService::class)->applyToPostedInvoice($event->move);
    }
}
