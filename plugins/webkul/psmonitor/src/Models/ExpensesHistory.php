<?php

namespace Webkul\Psmonitor\Models;

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
}