<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cloud extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'clouds';

    // public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'created',
        'modified',
    ];

    protected function casts(): array
    {
        return [
            'id'     => 'integer',
        ];
    }

    public function dynamicClients(): HasMany
    {
        return $this->hasMany(DynamicClient::class, 'cloud_id', 'id');
    }

    public function voucherSales(): HasMany
    {
        return $this->hasMany(VoucherSale::class, 'cloudID', 'id');
    }

    public function permanentUsers(): HasMany
    {
        return $this->hasMany(PermanentUser::class, 'cloud_id', 'id');
    }

    public function topUps(): HasMany
    {
        return $this->hasMany(TopUp::class, 'cloud_id', 'id');
    }
}
