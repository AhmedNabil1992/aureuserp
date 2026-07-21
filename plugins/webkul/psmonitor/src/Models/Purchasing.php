<?php

namespace Webkul\Psmonitor\Models;

class Purchasing extends RemoteModel
{
    protected $table = 'purchasing';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'InvoiceNo',
        'Barcode',
        'Quantity',
        'Price',
        'Amount',
        'INV_Date',
        'INV_Time',
        'Username',
        'Shift',
        'TRX_ID',
    ];

    protected $casts = [
        'ID' => 'integer',
        'InvoiceNo' => 'string',
        'Barcode' => 'string',
        'Quantity' => 'integer',
        'Price' => 'float',
        'Amount' => 'float',
        'INV_Date' => 'datetime',
        'INV_Time' => 'datetime',
        'Username' => 'string',
        'Shift' => 'string',
        'TRX_ID' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'Barcode', 'Code');
    }
}