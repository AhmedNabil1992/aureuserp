<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clouds extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'clouds';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'IsDist',
    ];

    public function dynamicClients()
    {
        return $this->hasMany(DynamicClients::class);
    }

    /**
     * Get the voucher sales for the cloud.
     */
    public function voucherSales()
    {
        return $this->hasMany(VoucherSales::class, 'cloudID');
    }

    public function permanentUsers()
    {
        return $this->hasMany(PermanentUser::class, 'cloud_id', 'id');
    }

    public function topups()
    {
        return $this->hasMany(Topups::class, 'cloud_id', 'id');
    }
}
