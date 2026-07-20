<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;

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

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }
}