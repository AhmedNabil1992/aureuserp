<?php

namespace Webkul\Psmonitor\Models;

class DevicePrice extends RemoteModel
{
    protected $table = 'device_price';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Device_Type',
        'Device_Name',
        'Game_Type',
        'Hour_Price',
        'S_From',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Device_Type' => 'integer',
        'Hour_Price' => 'decimal:2',
    ];
}