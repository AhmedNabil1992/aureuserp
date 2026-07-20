<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;
use Webkul\Psmonitor\Models\ItemMaster;
use Webkul\Psmonitor\Models\Device;

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
        'Notes'
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

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'Item_ID', 'Code');
    }
    public function device()
    {
        return $this->belongsTo(Device::class, 'Order_No', 'Order_No');
    }
}