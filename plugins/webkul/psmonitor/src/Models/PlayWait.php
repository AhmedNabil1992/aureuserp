<?php

namespace Webkul\Psmonitor\Models;

class PlayWait extends RemoteModel
{
    protected $table = 'play_wait';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Order_No',
        'Device_Name',
        'Start_Time',
        'End_Time',
        'Period',
        'Cost',
        'Play_Type',
        'Play_Price',
        'User_Name',
        'Shift',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Order_No' => 'integer',
        'Start_Time' => 'datetime',
        'End_Time' => 'datetime',
        'Period' => 'integer',
        'Cost' => 'decimal:2',
        'Play_Type' => 'string',
        'Play_Price' => 'decimal:2',
        'Shift' => 'integer',
        'User_Name' => 'string',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'Order_No', 'Order_No');
    }
}