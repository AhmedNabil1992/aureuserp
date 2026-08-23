<?php

namespace Webkul\TechnicalSupport\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TicketStatus: string implements HasColor, HasIcon, HasLabel
{
    case Open = 'open';
    case Pending = 'pending';
    case Closed = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Open    => __('technical-support::enums/ticket-status.open'),
            self::Pending => __('technical-support::enums/ticket-status.pending'),
            self::Closed  => __('technical-support::enums/ticket-status.closed'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Open    => 'success',
            self::Pending => 'warning',
            self::Closed  => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Open    => 'heroicon-o-lock-open',
            self::Pending => 'heroicon-o-clock',
            self::Closed  => 'heroicon-o-lock-closed',
        };
    }
}
