<?php

namespace Webkul\Psmonitor\Models;

class LoginHistory extends RemoteModel
{
    protected $table = 'login_hist';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
      'ID',
      'UserID',
      'UserName',
      'Date',
      'Time',
      'IPAddress',
      'Remark',
    ];

    public $casts = [
      'ID' => 'integer',
      'UserID' => 'integer',
      'UserName' => 'string',
      'Date' => 'datetime',
      'Time' => 'datetime',
      'IPAddress' => 'string',
      'Remark' => 'string',
    ];

}
