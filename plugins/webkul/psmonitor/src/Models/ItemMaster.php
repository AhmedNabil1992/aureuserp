<?php

namespace Webkul\Psmonitor\Models;

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
}
