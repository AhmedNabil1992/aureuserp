<?php

namespace Webkul\Psmonitor\Models;

use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Software\Models\License;
use InvalidArgumentException;
use Webkul\Psmonitor\Models\PlayWait;

class Shifts extends RemoteModel
{
    protected $table = 'shift_mgt';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Shift_No',
        'Shift_Date',
        'Shift_From',
        'Shift_To',
        'Shift_Open', // Shift Open By
        'Shift_Close', // Shift Close By
        'Start_AMT',
        'Playstation',
        'Sales_AMT',
        'Customer_Add',
        'Income_History',
        'Tax_History',
        'Services_History',
        'Purchase_AMT',
        'Expenses_AMT',
        'Discount',
        'Customer_Minus',
        'Customer_Credit',
        'Credit_AMT',
        'Remain_AMT', // Computed as (((((((([Start_AMT]+[Playstation])+[Sales_AMT])+[Customer_Add])+[Income_History])+[Tax_History])+[Services_History])-(((([Purchase_AMT]+[Expenses_AMT])+[Discount])+[Customer_Credit])+[Customer_Minus]))-[Credit_AMT])
        'Actual_Amt',
        'Different', // Computed as (((((((([Start_AMT]+[Playstation])+[Sales_AMT])+[Customer_Add])+[Income_History])+[Tax_History])+[Services_History])-(((([Purchase_AMT]+[Expenses_AMT])+[Discount])+[Customer_Credit])+[Customer_Minus]))-[Actual_AMT])
        'Status', // Open , Close
        'Notify', // True , False
    ];

    protected $casts = [
        'ID' => 'integer',
        'Shift_No' => 'integer',
        'Shift_Date' => 'date',
        'Shift_From' => 'datetime',
        'Shift_To' => 'datetime',
        'Start_AMT' => 'decimal:2',
        'Playstation' => 'decimal:2',
        'Sales_AMT' => 'decimal:2',
        'Customer_Add' => 'decimal:2',
        'Income_History' => 'decimal:2',
        'Tax_History' => 'decimal:2',
        'Services_History' => 'decimal:2',
        'Purchase_AMT' => 'decimal:2',
        'Expenses_AMT' => 'decimal:2',
        'Discount' => 'decimal:2',
        'Customer_Minus' => 'decimal:2',
        'Customer_Credit' => 'decimal:2',
        'Credit_AMT' => 'decimal:2',
        'Remain_AMT' => 'decimal:2',
        'Actual_Amt' => 'decimal:2',
        'Different' => 'decimal:2',
        'Status' => 'string',
        'Notify' => 'boolean',
    ];
    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->Server_IP, config('remote_access.remote_database', 'pstm'));
    }

    public function playWaits()
    {
        return $this->hasMany(PlayWait::class, 'Shift', 'Shift_No');
    }

    /**
     * إرجاع رقم الشيفت المفتوح حالياً على هوست معين
     * يُستخدم في الويدجت
     */
    public static function getOpenShiftNo(string $host, ?string $database = null): ?int
    {
        $shift = static::onHost($host, $database)
            ->where('Status', 'Open')
            ->orderByDesc('Shift_No')
            ->first(['Shift_No']);

        return $shift?->Shift_No;
    }
}