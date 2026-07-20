<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;

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
    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }
}