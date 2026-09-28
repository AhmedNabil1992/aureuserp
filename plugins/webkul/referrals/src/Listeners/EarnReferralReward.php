<?php

declare(strict_types=1);

namespace Webkul\Referral\Listeners;

use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Events\MovePaid;
use Webkul\Referral\Services\ReferralRewardService;

class EarnReferralReward
{
    public function __construct(private readonly ReferralRewardService $rewards) {}

    public function handle(MoveConfirmed|MovePaid $event): void
    {
        $this->rewards->earnForPaidMove($event->move);
    }
}
