<?php

namespace Webkul\SoftwareOnline\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InstanceStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Expired = 'expired';
    case Failed = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending   => __('software-online::enums/instance-status.pending'),
            self::Active    => __('software-online::enums/instance-status.active'),
            self::Suspended => __('software-online::enums/instance-status.suspended'),
            self::Expired   => __('software-online::enums/instance-status.expired'),
            self::Failed    => __('software-online::enums/instance-status.failed'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending   => 'warning',
            self::Active    => 'success',
            self::Suspended => 'danger',
            self::Expired   => 'gray',
            self::Failed    => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending   => 'heroicon-o-clock',
            self::Active    => 'heroicon-o-check-circle',
            self::Suspended => 'heroicon-o-pause-circle',
            self::Expired   => 'heroicon-o-x-circle',
            self::Failed    => 'heroicon-o-exclamation-triangle',
        };
    }
}
