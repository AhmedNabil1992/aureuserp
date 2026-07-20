<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;

class ExpensesHistory extends RemoteModel
{
    protected $table = 'expenses_history';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Expenses_Type',
        'Expenses_Remark',
        'Expenses_AMT',
        'Username',
        'Shift',
        'TRX_Date',
        'TRX_Time',
        'TRX_ID',
    ];
    protected $casts = [
        'ID' => 'integer',
        'Expenses_Type' => 'string',
        'Expenses_Remark' => 'string',
        'Expenses_AMT' => 'decimal:2',
        'Username' => 'string',
        'Shift' => 'string',
        'TRX_Date' => 'date',
        'TRX_Time' => 'string',
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