<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Support\Models\UOM;

class BillOfMaterialLine extends Model
{
    protected $table = 'products_bill_of_material_lines';

    protected $fillable = [
        'bill_of_material_id',
        'component_id',
        'quantity',
        'uom_id',
        'sort',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    public function billOfMaterial(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterial::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'component_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class);
    }
}
