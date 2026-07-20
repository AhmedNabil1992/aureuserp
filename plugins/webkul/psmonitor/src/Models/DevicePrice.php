<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;

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

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }
}