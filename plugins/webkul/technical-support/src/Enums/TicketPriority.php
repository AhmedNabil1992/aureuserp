<?php

namespace Webkul\TechnicalSupport\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TicketPriority: string implements HasColor, HasIcon, HasLabel
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';

    public function getLabel(): string
    {
        return match ($this) {
            self::Low    => __('technical-support::enums/ticket-priority.low'),
            self::Normal => __('technical-support::enums/ticket-priority.normal'),
            self::High   => __('technical-support::enums/ticket-priority.high'),
            self::Urgent => __('technical-support::enums/ticket-priority.urgent'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Low    => 'gray',
            self::Normal => 'info',
            self::High   => 'warning',
            self::Urgent => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Low    => 'heroicon-o-arrow-down',
            self::Normal => 'heroicon-o-minus',
            self::High   => 'heroicon-o-arrow-up',
            self::Urgent => 'heroicon-o-exclamation-triangle',
        };
    }
}
