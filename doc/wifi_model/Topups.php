<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topups extends Model
{
    protected $connection = 'mariadb';

    protected $table = 'top_ups';

    protected $fillable = [
        'id',
        'cloud_id',
        'permanent_user_id',
        'data',
        'time',
        'days_to_use',
        'comment',
        'data',
        'created',
        'updated',
    ];

    public function clouds()
    {
        return $this->belongsTo(Clouds::class, 'cloud_id', 'id');
    }

    public function permanentUser()
    {
        return $this->belongsTo(PermanentUser::class, 'permanent_user_id', 'id');
    }
}
