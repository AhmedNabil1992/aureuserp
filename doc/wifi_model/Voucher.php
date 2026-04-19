<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'vouchers';

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

    protected $casts = [
        'last_accept_time' => 'datetime',
    ];

    public function vouchers()
    {
        return $this->belongsTo(Voucher::class, 'batch', 'batch');
    }
}
