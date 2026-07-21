<?php

namespace Webkul\Psmonitor\Models;

class Customers extends RemoteModel
{
    protected $table = 'customer';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'C_ID',
        'C_Name',
        'C_Card',
        'C_Phone',
        'C_Money',
        'C_Point',
        'Remark',
        'Status',
    ];

    protected $casts = [
        'ID' => 'integer',
        'C_ID' => 'integer',
        'C_Name' => 'string',
        'C_Card' => 'string',
        'C_Phone' => 'string',
        'C_Money' => 'decimal:2',
        'C_Point' => 'integer',
        'Remark' => 'string',
        'Status' => 'boolean',
    ];
}