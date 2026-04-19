<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'vouchers';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'batch',
        'status',
        'perc_time_used',
        'perc_data_used',
        'last_accept_time',
        'last_reject_time',
        'last_accept_nas',
        'last_reject_nas',
        'last_reject_message',
        'cloud_id',
        'created',
        'modified',
        'extra_name',
        'extra_value',
        'password',
        'realm',
        'realm_id',
        'profile',
        'profile_id',
        'expire',
        'time_valid',
        'data_used',
        'data_cap',
        'time_used',
        'time_cap',
    ];

    protected function casts(): array
    {
        return [
            'id'               => 'integer',
            'cloud_id'         => 'integer',
            'realm_id'         => 'integer',
            'profile_id'       => 'integer',
            'last_accept_time' => 'datetime',
            'last_reject_time' => 'datetime',
            'created'          => 'datetime',
            'modified'         => 'datetime',
        ];
    }

    public function batchItems(): HasMany
    {
        return $this->hasMany(Voucher::class, 'batch', 'batch');
    }
}
