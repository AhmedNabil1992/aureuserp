<?php

namespace Webkul\Lead\Enums;

enum LeadTemperature: string
{
    case Hot = 'hot';
    case Warm = 'warm';
    case Cold = 'cold';

    public function getLabel(): string
    {
        return match ($this) {
            self::Hot  => __('leads::enums/lead-temperature.hot'),
            self::Warm => __('leads::enums/lead-temperature.warm'),
            self::Cold => __('leads::enums/lead-temperature.cold'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Hot  => 'danger',
            self::Warm => 'warning',
            self::Cold => 'info',
        };
    }
}
