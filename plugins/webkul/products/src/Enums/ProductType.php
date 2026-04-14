<?php

namespace Webkul\Product\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductType: string implements HasLabel
{
    case GOODS = 'goods';

    case SERVICE = 'service';

    case PRODUCT = 'product';

    public function getLabel(): string
    {
        return match ($this) {
            self::GOODS   => __('products::enums/product-type.goods'),
            self::SERVICE => __('products::enums/product-type.service'),
            self::PRODUCT => __('products::enums/product-type.product'),
        };
    }
}
