<?php

namespace Webkul\Wifi\Enums;

use Filament\Support\Contracts\HasLabel;

enum WifiPackageType: string implements HasLabel
{
    case Limited = 'limited';

    case Unlimited = 'unlimited';

    public function getLabel(): string
    {
        return match ($this) {
            self::Limited   => 'Limited',
            self::Unlimited => 'Unlimited',
        };
    }
}
