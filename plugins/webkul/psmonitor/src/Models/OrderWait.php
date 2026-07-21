<?php

namespace Webkul\Psmonitor\Models;

class OrderWait extends RemoteModel
{
    protected $table = 'order_wait';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Order_No',
        'Device_Name',
        'Item_ID',
        'Quantity',
        'Price',
        'Amount',
        'Print',
        'Order_By',
        'Notes',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Order_No' => 'integer',
        'Item_ID' => 'integer',
        'Quantity' => 'integer',
        'Price' => 'decimal:2',
        'Amount' => 'decimal:2',
        'Print' => 'boolean',
        'Order_By' => 'string',
        'Notes' => 'string',
    ];

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'Item_ID', 'Code');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'Order_No', 'Order_No');
    }
}