<?php

namespace Webkul\Account\Models;

use Webkul\Partner\Models\Partner;

class Customer extends Partner
{
    public function getMorphClass(): string
    {
        return Partner::class;
    }
}
