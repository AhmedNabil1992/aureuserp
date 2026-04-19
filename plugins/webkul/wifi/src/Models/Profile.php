<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
