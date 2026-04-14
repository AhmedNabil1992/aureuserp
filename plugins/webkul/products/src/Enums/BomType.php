<?php

namespace Webkul\Product\Enums;

use Filament\Support\Contracts\HasLabel;

enum BomType: string implements HasLabel
{
    case Manufacture = 'manufacture';
    case Kit = 'kit';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Manufacture => __('products::enums/bom-type.manufacture'),
            self::Kit         => __('products::enums/bom-type.kit'),
        };
    }
}
