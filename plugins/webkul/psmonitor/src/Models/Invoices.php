<?php

namespace Webkul\Psmonitor\Models;

class Invoices extends RemoteModel
{
    protected $table = 'invoices';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Invoice_No',
        'Date',
        'Time',
        'Shift_No',
        'Amount',
        'Discount',
        'Services',
        'Tax',
        'Total',
        'Username',
        'Customer_ID',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Invoice_No' => 'string',
        'Date' => 'date',
        'Time' => 'string',
        'Shift_No' => 'integer',
        'Amount' => 'decimal:2',
        'Discount' => 'decimal:2',
        'Services' => 'decimal:2',
        'Tax' => 'decimal:2',
        'Total' => 'decimal:2',
        'Username' => 'string',
        'Customer_ID' => 'string',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'Customer_ID', 'C_Card');
    }
}