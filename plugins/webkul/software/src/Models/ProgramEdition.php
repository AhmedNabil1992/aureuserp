<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Product\Models\Product;

class ProgramEdition extends Model
{
    use HasFactory;

    protected $table = 'software_program_editions';

    protected $fillable = [
        'program_id',
        'product_id',
        'name',
        'max_devices',
        'license_cost',
        'license_price',
        'monthly_renewal',
        'annual_renewal',
    ];

    protected $casts = [
        'max_devices'     => 'integer',
        'license_cost'    => 'decimal:2',
        'license_price'   => 'decimal:2',
        'monthly_renewal' => 'decimal:2',
        'annual_renewal'  => 'decimal:2',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'edition_id');
    }
}
