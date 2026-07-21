<?php

namespace Webkul\Psmonitor\Models;

class Users extends RemoteModel
{
    protected $table = 'users';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'User_ID',
        'Username',
        'Password',
        'Position',
        'Permission_Type',
        'Skinname',
        'IsActive',
    ];

    protected $casts = [
        'ID' => 'integer',
        'User_ID' => 'string',
        'Username' => 'string',
        'Password' => 'string',
        'Position' => 'string',
        'Permission_Type' => 'string',
        'Skinname' => 'string',
        'IsActive' => 'boolean',
    ];
}