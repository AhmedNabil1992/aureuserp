<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherSales extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'sales';

    // protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'cloudID',
        'nasidentifier',
        'Date',
        'SCount',
    ];

    /**
     * Get the cloud that owns the voucher sale.
     */
    public function cloud()
    {
        return $this->belongsTo(Clouds::class, 'cloudID');
    }

    // /**
    //  * Get the dynamic client that owns the voucher sale.
    //  */
    public function dynamicClient()
    {
        return $this->belongsTo(DynamicClients::class, 'nasidentifier', 'nasidentifier');
    }
}
