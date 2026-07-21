<?php

namespace Webkul\Psmonitor\Models;

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
}