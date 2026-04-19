<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Radacct extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'radacct';

    protected $primaryKey = 'radacctid';

    public $timestamps = false;

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

    protected function casts(): array
    {
        return [
            'radacctid'       => 'integer',
            'acctstarttime'   => 'datetime',
            'acctstoptime'    => 'datetime',
            'acctsessiontime' => 'integer',
        ];
    }

    public function dynamicClient(): BelongsTo
    {
        return $this->belongsTo(DynamicClient::class, 'nasidentifier', 'nasidentifier');
    }
}
