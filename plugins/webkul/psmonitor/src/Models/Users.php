<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;

class Users extends RemoteModel
{
    protected $table = 'users';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'User_ID',
        'Username',
        'Password',
        'Position',
        'Permission_Type',
        'Skinname',
        'IsActive',
    ];

    protected $casts = [
        'ID' => 'integer',
        'User_ID' => 'string',
        'Username' => 'string',
        'Password' => 'string',
        'Position' => 'string',
        'Permission_Type' => 'string',
        'Skinname' => 'string',
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