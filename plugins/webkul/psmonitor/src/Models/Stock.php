<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Models\ItemMaster;
use Webkul\Software\Models\License;
use InvalidArgumentException;

class Stock extends RemoteModel
{
    protected $table = 'stock';

    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $fillable = [
        'ID',
        'Barcode',
        'Quantity',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Quantity' => 'integer',
    ];

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }

    public function item()
    {
        return $this->belongsTo(ItemMaster::class, 'Barcode', 'Code');
    }
    }