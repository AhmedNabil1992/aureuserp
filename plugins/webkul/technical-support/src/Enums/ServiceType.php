<?php

namespace Webkul\TechnicalSupport\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ServiceType: string implements HasColor, HasIcon, HasLabel
{
    case Software = 'software';
    case Wifi = 'wifi';
    case OnlineService = 'online_service';

    public function getLabel(): string
    {
        return match ($this) {
            self::Software      => __('technical-support::enums/service-type.software'),
            self::Wifi          => __('technical-support::enums/service-type.wifi'),
            self::OnlineService => __('technical-support::enums/service-type.online_service'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Software      => 'primary',
            self::Wifi          => 'success',
            self::OnlineService => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Software      => 'heroicon-o-computer-desktop',
            self::Wifi          => 'heroicon-o-wifi',
            self::OnlineService => 'heroicon-o-globe-alt',
        };
    }
}
