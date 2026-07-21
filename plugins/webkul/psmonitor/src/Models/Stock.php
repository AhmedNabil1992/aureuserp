<?php

namespace Webkul\Psmonitor\Models;

class Stock extends RemoteModel
{
    protected $table = 'stock';

    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $fillable = [
        'ID',
        'Barcode',
        'Quantity',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Quantity' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'Barcode', 'Code');
    }
}