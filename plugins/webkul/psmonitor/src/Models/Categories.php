<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Models\ItemMaster;
use Webkul\Software\Models\License;
use InvalidArgumentException;

class Categories extends RemoteModel
{
    protected $table = 'categiores';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Cate_Name',
        'Remark',
        'IsActive',
    ];
    protected $casts = [
        'ID' => 'integer',
        'Cate_Name' => 'string',
        'Remark' => 'string',
        'IsActive' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(ItemMaster::class, 'Group_ID', 'ID');
    }

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }
}
