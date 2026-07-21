<?php

namespace Webkul\Psmonitor\Models;

class DeviceType extends RemoteModel
{
    protected $table = 'Device_Type';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Name',
        'Description',
        'IsActive',
    ];

    protected $casts = [
        'ID' => 'integer',
        'IsActive' => 'boolean',
    ];
}