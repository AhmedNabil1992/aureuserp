<?php

declare(strict_types=1);

namespace Webkul\Vpn\Support;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

class SoftEtherValue
{
    public static function date(mixed $value): string
    {
        if (blank($value) || str_starts_with((string) $value, '1970-01-01')) {
            return '—';
        }

        try {
            return Carbon::parse($value)->timezone(config('app.timezone'))->format('Y-m-d H:i:s');
        } catch (Throwable) {
            return (string) $value;
        }
    }

    public static function label(string $key): string
    {
        return Str::of($key)
            ->replace(['policy:', '_u32', '_u64', '_str', '_utf', '_bool', '_dt', '_ip'], ['', '', '', '', '', '', '', ''])
            ->replace(['.', '_'], ' ')
            ->headline()
            ->toString();
    }

    public static function display(string $key, mixed $value): string
    {
        if (str_ends_with($key, '_dt')) {
            return self::date($value);
        }

        if (is_bool($value)) {
            return $value ? __('vpn::app.common.yes') : __('vpn::app.common.no');
        }

        if ($value === null || $value === '') {
            return '—';
        }

        return (string) $value;
    }
}
