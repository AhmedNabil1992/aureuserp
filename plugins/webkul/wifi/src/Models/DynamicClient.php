<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicClient extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'dynamic_clients';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'nasidentifier',
        'last_contact',
        'last_contact_ip',
        'active',
        'cloud_id',
        'Picture',
        'zero_ip',
        'created',
        'modified',
    ];

    protected function casts(): array
    {
        return [
            'id'           => 'integer',
            'cloud_id'     => 'integer',
            'last_contact' => 'datetime',
        ];
    }

    public function cloud(): BelongsTo
    {
        return $this->belongsTo(Cloud::class, 'cloud_id', 'id');
    }

    public function dynamicClientRealms(): HasMany
    {
        return $this->hasMany(DynamicClientRealm::class, 'dynamic_client_id', 'id');
    }

    public function voucherSales(): HasMany
    {
        return $this->hasMany(VoucherSale::class, 'nasidentifier', 'nasidentifier');
    }

    public function radaccts(): HasMany
    {
        return $this->hasMany(Radacct::class, 'nasidentifier', 'nasidentifier');
    }

    public function scopeAccessPoints(Builder $query): Builder
    {
        return $query->whereNotNull('zero_ip')->where('zero_ip', '!=', '');
    }
}
