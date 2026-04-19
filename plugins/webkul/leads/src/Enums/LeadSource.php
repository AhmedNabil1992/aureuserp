<?php

namespace Webkul\Lead\Enums;

enum LeadSource: string
{
    case Facebook = 'facebook';
    case Google = 'google';
    case Referral = 'referral';
    case WalkIn = 'walk_in';
    case ColdCall = 'cold_call';
    case Website = 'website';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Facebook => __('leads::enums/lead-source.facebook'),
            self::Google   => __('leads::enums/lead-source.google'),
            self::Referral => __('leads::enums/lead-source.referral'),
            self::WalkIn   => __('leads::enums/lead-source.walk_in'),
            self::ColdCall => __('leads::enums/lead-source.cold_call'),
            self::Website  => __('leads::enums/lead-source.website'),
            self::Other    => __('leads::enums/lead-source.other'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Facebook => 'primary',
            self::Google   => 'warning',
            self::Referral => 'success',
            self::WalkIn   => 'info',
            self::ColdCall => 'gray',
            self::Website  => 'purple',
            self::Other    => 'gray',
        };
    }
}
