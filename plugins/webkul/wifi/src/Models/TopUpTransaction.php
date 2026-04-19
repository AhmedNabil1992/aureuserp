<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopUpTransaction extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'top_up_transactions';

    public $timestamps = false;

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

    protected function casts(): array
    {
        return [
            'id'                => 'integer',
            'user_id'           => 'integer',
            'permanent_user_id' => 'integer',
            'top_up_id'         => 'integer',
            'created'           => 'datetime',
            'updated'           => 'datetime',
        ];
    }

    public function permanentUserModel(): BelongsTo
    {
        return $this->belongsTo(PermanentUser::class, 'permanent_user_id', 'id');
    }

    public function topUp(): BelongsTo
    {
        return $this->belongsTo(TopUp::class, 'top_up_id', 'id');
    }
}
