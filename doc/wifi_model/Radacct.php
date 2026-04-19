<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radacct extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'radacct';

    public $timestamps = false;

    protected $primaryKey = 'radacctid';

    protected $fillable = [
        'username',
        'realm',
        'nasidentifier',
        'acctstarttime',
        'acctstoptime',
        'acctsessiontime',
        'acctinputoctets',
        'acctoutputoctets',
        'callingstationid',
        'framedipaddress',
        'acctterminatecause',
    ];

    // i want to link nasidentifier to nas table
    public function dynamicClient()
    {
        return $this->belongsTo(DynamicClients::class, 'nasidentifier', 'nasidentifier');
    }
}
