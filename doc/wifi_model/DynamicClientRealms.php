<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicClientRealms extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'dynamic_client_realms';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'dynamic_client_id',
        'realm_id',
    ];

    public function dynamicClient()
    {
        return $this->belongsTo(DynamicClients::class, 'dynamic_client_id');
    }
}
