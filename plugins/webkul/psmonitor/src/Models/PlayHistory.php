<?php

namespace Webkul\Psmonitor\Models;

class PlayHistory extends RemoteModel
{
    protected $table = 'play_history';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Device_Name',
        'Play_Type',
        'Hour_Price',
        'Start_Time',
        'End_Time',
        'Actual_End_Time',
        'Play_Time', // in minutes
        'Cost',
        'Username',
        'Shift_No',
        'Invoice_No',
        'TRX_Date',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Hour_Price' => 'decimal:2',
        'Start_Time' => 'datetime',
        'End_Time' => 'datetime',
        'Actual_End_Time' => 'datetime',
        'Play_Time' => 'integer',
        'Cost' => 'decimal:2',
        'Shift_No' => 'integer',
        'Invoice_No' => 'string',
        'TRX_Date' => 'date',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'Device_Name', 'Device_Name');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoices::class, 'Invoice_No', 'Invoice_No');
    }
}