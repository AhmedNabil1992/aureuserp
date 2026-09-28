<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Listeners;

use Webkul\Account\Events\MovePaid;
use Webkul\SoftwareOnline\Models\OnlineInstanceTransaction;

class MarkOnlineTransactionsPaid
{
    public function handle(MovePaid $event): void
    {
        OnlineInstanceTransaction::query()
            ->where('move_id', $event->move->id)
            ->where('status', '!=', 'paid')
            ->update(['status' => 'paid']);
    }
}
