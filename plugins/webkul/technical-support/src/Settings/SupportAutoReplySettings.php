<?php

namespace Webkul\TechnicalSupport\Settings;

use Spatie\LaravelSettings\Settings;

class SupportAutoReplySettings extends Settings
{
    public bool $is_auto_reply_enabled;

    public ?string $welcome_message;

    public bool $is_emergency_mode;

    public ?string $emergency_message;

    public bool $is_business_hours_enabled;

    public array $work_days;

    public ?string $work_start_time;

    public ?string $work_end_time;

    public string $timezone;

    public ?string $out_of_hours_message;

    public static function group(): string
    {
        return 'technical_support_auto_reply';
    }
}
