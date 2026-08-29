<?php

namespace Webkul\SoftwareOnline\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BillingCycle: string implements HasLabel, HasColor
{
    case Trial   = 'trial';
    case Monthly = 'monthly';
    case Annual  = 'annual';

    public function getLabel(): string
    {
        return match ($this) {
            self::Trial   => __('software-online::enums/billing-cycle.trial'),
            self::Monthly => __('software-online::enums/billing-cycle.monthly'),
            self::Annual  => __('software-online::enums/billing-cycle.annual'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Trial   => 'warning',
            self::Monthly => 'info',
            self::Annual  => 'success',
        };
    }
}
