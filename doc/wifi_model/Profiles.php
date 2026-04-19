<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profiles extends Model
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
}
