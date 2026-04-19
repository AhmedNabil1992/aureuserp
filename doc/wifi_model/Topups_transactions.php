<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topups_transactions extends Model
{
    protected $connection = 'mariadb';

    protected $table = 'top_up_transactions';

    protected $fillable = [
        'id',
        'user_id',
        'permanent_user_id',
        'permanent_user',
        'top_up_id',
        'type',
        'action',
        'radius_attribute',
        'old_value',
        'new_value',
        'created',
        'updated',
    ];

    public function permanentUser()
    {
        return $this->belongsTo(PermanentUser::class, 'permanent_user_id', 'id');
    }

    public function topup()
    {
        return $this->belongsTo(Topups::class, 'top_up_id', 'id');
    }
}
