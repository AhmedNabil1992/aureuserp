<?php

namespace Webkul\Psmonitor\Models;

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
}
