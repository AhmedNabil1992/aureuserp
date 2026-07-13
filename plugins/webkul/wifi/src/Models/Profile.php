<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Wifi\Models\PermanentUser;

class Profile extends Model
{
    use HasFactory;

    protected $connection = 'mariadb';

    protected $table = 'profiles';

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

    public function permanentUsers(): HasMany
    {
        return $this->hasMany(PermanentUser::class, 'profile_id', 'id');
    }
}
