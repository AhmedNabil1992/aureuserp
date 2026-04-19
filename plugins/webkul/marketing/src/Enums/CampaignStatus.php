<?php

namespace Webkul\Marketing\Enums;

enum CampaignStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Paused = 'paused';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft     => __('marketing::enums/campaign-status.draft'),
            self::Active    => __('marketing::enums/campaign-status.active'),
            self::Completed => __('marketing::enums/campaign-status.completed'),
            self::Paused    => __('marketing::enums/campaign-status.paused'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft     => 'gray',
            self::Active    => 'success',
            self::Completed => 'info',
            self::Paused    => 'warning',
        };
    }
}
