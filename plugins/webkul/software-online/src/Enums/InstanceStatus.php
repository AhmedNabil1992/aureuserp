<?php

namespace Webkul\SoftwareOnline\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InstanceStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Provisioning = 'provisioning';
    case Active = 'active';
    case Suspended = 'suspended';
    case Expired = 'expired';
    case Failed = 'failed';
    case Deleting = 'deleting';
    case Deleted = 'deleted';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending      => __('software-online::enums/instance-status.pending'),
            self::Provisioning => __('software-online::enums/instance-status.provisioning'),
            self::Active       => __('software-online::enums/instance-status.active'),
            self::Suspended    => __('software-online::enums/instance-status.suspended'),
            self::Expired      => __('software-online::enums/instance-status.expired'),
            self::Failed       => __('software-online::enums/instance-status.failed'),
            self::Deleting     => __('software-online::enums/instance-status.deleting'),
            self::Deleted      => __('software-online::enums/instance-status.deleted'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending      => 'warning',
            self::Provisioning => 'info',
            self::Active       => 'success',
            self::Suspended    => 'danger',
            self::Expired      => 'gray',
            self::Failed       => 'danger',
            self::Deleting     => 'warning',
            self::Deleted      => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending      => 'heroicon-o-clock',
            self::Provisioning => 'heroicon-o-arrow-path',
            self::Active       => 'heroicon-o-check-circle',
            self::Suspended    => 'heroicon-o-pause-circle',
            self::Expired      => 'heroicon-o-x-circle',
            self::Failed       => 'heroicon-o-exclamation-triangle',
            self::Deleting     => 'heroicon-o-trash',
            self::Deleted      => 'heroicon-o-x-circle',
        };
    }
}
