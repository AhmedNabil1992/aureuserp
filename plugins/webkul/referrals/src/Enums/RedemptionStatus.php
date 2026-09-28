<?php

declare(strict_types=1);

namespace Webkul\Referral\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RedemptionStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Earned = 'earned';
    case Reversed = 'reversed';
    case Void = 'void';

    public function getLabel(): string
    {
        return __("referrals::app.statuses.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending              => 'warning',
            self::Earned               => 'success',
            self::Reversed, self::Void => 'danger',
        };
    }
}
