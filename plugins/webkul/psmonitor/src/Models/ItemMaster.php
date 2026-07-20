<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\Categories;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;

class ItemMaster extends RemoteModel
{
    protected $table = 'item_master';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Group_ID',
        'Code',
        'Item_Name',
        'Item_Price',
        'Table_Price',
        'Direct_Price',
        'Direct_Price',
        'IsProduct',
        'IsSales',
        'Min_Stock_Alert',
        'IsActive',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Group_ID' => 'integer',
        'Code' => 'integer',
        'Item_Name' => 'string',
        'Item_Price' => 'decimal:2',
        'Table_Price' => 'decimal:2',
        'Direct_Price' => 'decimal:2',
        'IsProduct' => 'boolean',
        'IsSales' => 'boolean',
        'Min_Stock_Alert' => 'integer',
        'IsActive' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Categories::class, 'Group_ID', 'ID');
    }
    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }
}
