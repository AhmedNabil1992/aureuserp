<?php

namespace Webkul\SoftwareOnline\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransactionType: string implements HasLabel, HasColor
{
    case NewSubscription = 'new_subscription';
    case Renewal         = 'renewal';
    case Upgrade         = 'upgrade';

    public function getLabel(): string
    {
        return match ($this) {
            self::NewSubscription => __('software-online::enums/transaction-type.new_subscription'),
            self::Renewal         => __('software-online::enums/transaction-type.renewal'),
            self::Upgrade         => __('software-online::enums/transaction-type.upgrade'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NewSubscription => 'success',
            self::Renewal         => 'info',
            self::Upgrade         => 'warning',
        };
    }
}
