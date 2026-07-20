<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Models\Device;
use Webkul\Psmonitor\Models\Invoices;
use Webkul\Software\Models\License;
use InvalidArgumentException;

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
        'TRX_Date'
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

    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'Device_Name', 'Device_Name');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoices::class, 'Invoice_No', 'Invoice_No');
    }
}