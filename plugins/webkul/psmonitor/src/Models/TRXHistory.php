<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;

class TRXHistory extends RemoteModel
{
    protected $table = 'trx_history';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'TRX_Type',
        'TRX_Name',
        'TRX_Date',
        'TRX_Time',
        'Amount',
        'Shift',
        'Username',
        'Reference',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Amount' => 'float',
        'TRX_Date' => 'date',
        'TRX_Time' => 'string',
        'Shift' => 'string',
        'Username' => 'string',
        'Reference' => 'string',
    ];

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }
}