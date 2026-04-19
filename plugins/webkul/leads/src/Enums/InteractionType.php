<?php

namespace Webkul\Lead\Enums;

enum InteractionType: string
{
    case Call = 'call';
    case Sms = 'sms';
    case Email = 'email';
    case Meeting = 'meeting';
    case Whatsapp = 'whatsapp';
    case Visit = 'visit';
    case Note = 'note';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Call     => __('leads::enums/interaction-type.call'),
            self::Sms      => __('leads::enums/interaction-type.sms'),
            self::Email    => __('leads::enums/interaction-type.email'),
            self::Meeting  => __('leads::enums/interaction-type.meeting'),
            self::Whatsapp => __('leads::enums/interaction-type.whatsapp'),
            self::Visit    => __('leads::enums/interaction-type.visit'),
            self::Note     => __('leads::enums/interaction-type.note'),
            self::Other    => __('leads::enums/interaction-type.other'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Call     => 'success',
            self::Sms      => 'info',
            self::Email    => 'primary',
            self::Meeting  => 'warning',
            self::Whatsapp => 'success',
            self::Visit    => 'purple',
            self::Note     => 'gray',
            self::Other    => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Call     => 'heroicon-o-phone',
            self::Sms      => 'heroicon-o-chat-bubble-left',
            self::Email    => 'heroicon-o-envelope',
            self::Meeting  => 'heroicon-o-users',
            self::Whatsapp => 'heroicon-o-chat-bubble-left-right',
            self::Visit    => 'heroicon-o-map-pin',
            self::Note     => 'heroicon-o-document-text',
            self::Other    => 'heroicon-o-ellipsis-horizontal',
        };
    }
}
