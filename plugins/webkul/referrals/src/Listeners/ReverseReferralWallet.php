<?php

declare(strict_types=1);

namespace Webkul\Referral\Listeners;

use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveReversed;
use Webkul\Referral\Services\ReferralWalletService;

class ReverseReferralWallet
{
    public function handle(MoveCancelled|MoveReversed $event): void
    {
        app(ReferralWalletService::class)->reverseInvoiceUsage($event->move);
    }
}
