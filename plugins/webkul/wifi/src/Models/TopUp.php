<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopUp extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'top_ups';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'cloud_id',
        'permanent_user_id',
        'data',
        'time',
        'days_to_use',
        'comment',
        'created',
        'updated',
    ];

    protected function casts(): array
    {
        return [
            'id'                => 'integer',
            'cloud_id'          => 'integer',
            'permanent_user_id' => 'integer',
            'data'              => 'integer',
            'time'              => 'integer',
            'days_to_use'       => 'integer',
            'created'           => 'datetime',
            'updated'           => 'datetime',
        ];
    }

    public function cloud(): BelongsTo
    {
        return $this->belongsTo(Cloud::class, 'cloud_id', 'id');
    }

    public function permanentUser(): BelongsTo
    {
        return $this->belongsTo(PermanentUser::class, 'permanent_user_id', 'id');
    }
}
