<?php

namespace Webkul\Psmonitor\Models;

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
}