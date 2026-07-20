<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;

class ExpensesType extends RemoteModel
{
    protected $table = 'expenses_type';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Expenses_Type',
        'Expenses_Remark',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Expenses_Type' => 'string',
        'Expenses_Remark' => 'string',
    ];

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }
}