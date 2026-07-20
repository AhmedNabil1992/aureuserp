<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;

class Discount extends RemoteModel
{
    protected $table = 'discount_history';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Invoice_No',
        'Amount',
        'Reason',
        'Username',
        'Date',
        'Time',
        'Shift_No',
        'TRX_ID',
    ];
    protected $casts = [
        'ID' => 'integer',
        'Amount' => 'float',
        'Shift_No' => 'integer',
        'TRX_ID' => 'integer',
    ];

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }
}