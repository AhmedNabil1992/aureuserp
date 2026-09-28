<?php

declare(strict_types=1);

namespace Webkul\Referral\Enums;

use Filament\Support\Contracts\HasLabel;

enum DiscountType: string implements HasLabel
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';

    public function getLabel(): string
    {
        return __("referrals::app.discount_types.{$this->value}");
    }
}
