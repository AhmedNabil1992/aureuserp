<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;
use Webkul\Psmonitor\Models\ItemMaster;
use Webkul\Psmonitor\Models\Invoices;

class MarketHistory extends RemoteModel
{
    protected $table = 'market_history';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Client_Name', // this is device name in PS system
        'Item_ID',
        'Quantity',
        'Price',
        'Amount',
        'TRX_Date',
        'TRX_Time',
        'Username',
        'Shift',
        'Invoice_No',
        'TRX_ID'
    ];

    protected $casts = [
        'ID' => 'integer',
        'Item_ID' => 'integer',
        'Quantity' => 'integer',
        'Price' => 'decimal:2',
        'Amount' => 'decimal:2',
        'TRX_Date' => 'date',
        'TRX_Time' => 'string',
        'Shift' => 'integer',
        'Invoice_No' => 'string',
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
        return $this->belongsTo(ItemMaster::class, 'Item_ID', 'Code');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoices::class, 'Invoice_No', 'Invoice_No');
    }
}