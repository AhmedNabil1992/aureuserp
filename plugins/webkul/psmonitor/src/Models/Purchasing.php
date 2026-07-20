<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Models\ItemMaster;
use Webkul\Software\Models\License;
use InvalidArgumentException;

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

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'Barcode', 'Code');
    }
}