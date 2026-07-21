<?php

namespace Webkul\Psmonitor\Models;

class Device extends RemoteModel
{
    protected $table = 'devices';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Device_Type',
        'Device_Name',
        'IP_Address',
        'Play_Type',
        'Play_Price',
        'Start_Time',
        'End_Time',
        'Status_IMG',
        'Status',
        'Period',
        'Play_Cate',
        'Order_No',
        'Limit_Time',
        'IsActive',
        'Kind',
        'Invitation',
        'MAC_Address',
        'Customer_ID',
        'Deposit',
        'Session_ID',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Device_Type' => 'integer',
        'Play_Price' => 'decimal:2',
        'Start_Time' => 'datetime',
        'End_Time' => 'datetime',
        'Period' => 'integer',
        'Order_No' => 'integer',
        'Limit_Time' => 'integer',
        'IsActive' => 'boolean',
        'Deposit' => 'integer',
    ];

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'Device_Type', 'ID');
    }
}