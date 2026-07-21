<?php

namespace Webkul\Psmonitor\Models;

class Offers extends RemoteModel
{
    protected $table = 'Offers';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Name',
        'Devices',
        'Time_From',
        'Time_To',
        'Period',
        'Play_Type',
        'Price',
        'Remark',
        'IsActive',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Name' => 'string',
        'Devices' => 'string',
        'Time_From' => 'string',
        'Time_To' => 'string',
        'Period' => 'integer',
        'Play_Type' => 'string',
        'Price' => 'float',
        'Remark' => 'string',
        'IsActive' => 'boolean',
    ];
}