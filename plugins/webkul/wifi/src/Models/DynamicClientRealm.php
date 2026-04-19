<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicClientRealm extends Model
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

    protected function casts(): array
    {
        return [
            'id'                => 'integer',
            'dynamic_client_id' => 'integer',
            'realm_id'          => 'integer',
        ];
    }

    public function dynamicClient(): BelongsTo
    {
        return $this->belongsTo(DynamicClient::class, 'dynamic_client_id', 'id');
    }

    public function realm(): BelongsTo
    {
        return $this->belongsTo(Realm::class, 'realm_id', 'id');
    }
}
