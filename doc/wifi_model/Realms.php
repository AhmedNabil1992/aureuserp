<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Realms extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'realms';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'cloud_id',
    ];

    public function dynamicClients()
    {
        return $this->hasMany(DynamicClients::class);
    }

    public function clouds()
    {
        return $this->hasMany(Clouds::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Vouchers::class, 'realm_id', 'id');
    }
}
