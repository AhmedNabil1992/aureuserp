<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Subdomain implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^[a-z0-9](?:[a-z0-9-]{0,48}[a-z0-9])?$/', $value)) {
            $fail(__('software-online::validation.subdomain_format'));

            return;
        }

        if (in_array($value, config('software-online.reserved_subdomains', []), true)) {
            $fail(__('software-online::validation.subdomain_reserved'));
        }
    }
}
