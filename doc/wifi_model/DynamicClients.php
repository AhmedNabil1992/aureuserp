<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicClients extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'dynamic_clients';

    // protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'nasidentifier',
        'last_contact',
        'last_contact_ip',
        'cloud_id',
        'Picture',
        'zero_ip',
    ];

    protected $casts = [
        'id',
        'name',
        'nasidentifier',
        'last_contact',
        'last_contact_ip',
        'cloud_id',
        'Picture',
        'zero_ip',
    ];

    public function cloud()
    {
        return $this->belongsTo(Clouds::class);
    }

    public function dynamicClientRealms()
    {
        return $this->hasMany(DynamicClientRealms::class, 'dynamic_client_id');
    }

    /**
     * Get the voucher sales for the dynamic client.
     */
    public function voucherSales()
    {
        return $this->hasMany(VoucherSales::class, 'nasidentifier', 'nasidentifier');
    }

    public function radacct()
    {
        return $this->hasMany(Radacct::class, 'nasidentifier', 'nasidentifier');
    }

    /**
     * Scope to get only access points (records with IP addresses)
     */
    public function scopeAccessPoints($query)
    {
        return $query->where('zero_ip', '!=', '')->whereNotNull('zero_ip');
    }
}
