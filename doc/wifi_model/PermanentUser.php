<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermanentUser extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'permanent_users';

    protected $primaryKey = 'id';

    protected $casts = [
        'id'               => 'integer',
        'username'         => 'string',
        'name'             => 'string',
        'active'           => 'boolean',
        'last_accept_time' => 'datetime',
        'last_reject_time' => 'datetime',
        'last_accept_nas'  => 'string',
        'last_reject_nas'  => 'string',
        'realm'            => 'string',
        'realm_id'         => 'integer',
        'profile'          => 'string',
        'profile_id'       => 'integer',
        'cloud_id'         => 'integer',
        'created'          => 'datetime',
        'modified'         => 'datetime',
    ];

    public function clouds()
    {
        return $this->belongsTo(Clouds::class, 'cloud_id', 'id');
    }

    public function topups()
    {
        return $this->hasMany(Topups::class, 'permanent_user_id', 'id');
    }
}
