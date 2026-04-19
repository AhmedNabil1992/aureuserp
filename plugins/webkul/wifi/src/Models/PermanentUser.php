<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermanentUser extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'permanent_users';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'id'               => 'integer',
            'active'           => 'boolean',
            'realm_id'         => 'integer',
            'profile_id'       => 'integer',
            'cloud_id'         => 'integer',
            'last_accept_time' => 'datetime',
            'last_reject_time' => 'datetime',
            'created'          => 'datetime',
            'modified'         => 'datetime',
        ];
    }

    public function cloud(): BelongsTo
    {
        return $this->belongsTo(Cloud::class, 'cloud_id', 'id');
    }

    public function topUps(): HasMany
    {
        return $this->hasMany(TopUp::class, 'permanent_user_id', 'id');
    }
}
