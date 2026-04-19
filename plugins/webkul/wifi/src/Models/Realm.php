<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Realm extends Model
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

    protected function casts(): array
    {
        return [
            'id'       => 'integer',
            'cloud_id' => 'integer',
        ];
    }

    public function cloud(): BelongsTo
    {
        return $this->belongsTo(Cloud::class, 'cloud_id', 'id');
    }

    public function dynamicClientRealms(): HasMany
    {
        return $this->hasMany(DynamicClientRealm::class, 'realm_id', 'id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'realm_id', 'id');
    }
}
