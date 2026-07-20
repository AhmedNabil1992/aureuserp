<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;
use Webkul\Psmonitor\Models\Customers;

class Invoices extends RemoteModel
{
    protected $table = 'invoices';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Invoice_No',
        'Date',
        'Time',
        'Shift_No',
        'Amount',
        'Discount',
        'Services',
        'Tax',
        'Total',
        'Username',
        'Customer_ID',
    ];
    protected $casts = [
        'ID' => 'integer',
        'Invoice_No' => 'string',
        'Date' => 'date',
        'Time' => 'string',
        'Shift_No' => 'integer',
        'Amount' => 'decimal:2',
        'Discount' => 'decimal:2',
        'Services' => 'decimal:2',
        'Tax' => 'decimal:2',
        'Total' => 'decimal:2',
        'Username' => 'string',
        'Customer_ID' => 'string',
    ];
    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'Customer_ID', 'C_Card');
    }

}