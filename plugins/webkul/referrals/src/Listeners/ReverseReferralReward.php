<?php

declare(strict_types=1);

namespace Webkul\Referral\Listeners;

use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveReversed;
use Webkul\Referral\Services\ReferralRewardService;

class ReverseReferralReward
{
    public function __construct(private readonly ReferralRewardService $rewards) {}

    public function handle(MoveCancelled|MoveReversed $event): void
    {
        $this->rewards->reverseForMove($event->move);
    }
}
