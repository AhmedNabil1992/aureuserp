<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherSale extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'sales';

    public $timestamps = false;

    protected $fillable = [
        'cloudID',
        'nasidentifier',
        'Date',
        'SCount',
    ];

    protected function casts(): array
    {
        return [
            'cloudID' => 'integer',
            'Date'    => 'datetime',
            'SCount'  => 'integer',
        ];
    }

    public function cloud(): BelongsTo
    {
        return $this->belongsTo(Cloud::class, 'cloudID', 'id');
    }

    public function dynamicClient(): BelongsTo
    {
        return $this->belongsTo(DynamicClient::class, 'nasidentifier', 'nasidentifier');
    }
}
