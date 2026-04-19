<?php

namespace Webkul\Marketing\Enums;

enum AdPlatform: string
{
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case Google = 'google';
    case TikTok = 'tiktok';
    case Snapchat = 'snapchat';
    case Youtube = 'youtube';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Facebook  => __('marketing::enums/ad-platform.facebook'),
            self::Instagram => __('marketing::enums/ad-platform.instagram'),
            self::Google    => __('marketing::enums/ad-platform.google'),
            self::TikTok    => __('marketing::enums/ad-platform.tiktok'),
            self::Snapchat  => __('marketing::enums/ad-platform.snapchat'),
            self::Youtube   => __('marketing::enums/ad-platform.youtube'),
            self::Other     => __('marketing::enums/ad-platform.other'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Facebook  => 'primary',
            self::Instagram => 'pink',
            self::Google    => 'warning',
            self::TikTok    => 'gray',
            self::Snapchat  => 'warning',
            self::Youtube   => 'danger',
            self::Other     => 'gray',
        };
    }
}
