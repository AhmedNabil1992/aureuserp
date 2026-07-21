<?php

namespace Webkul\Psmonitor\Models;

class ExpensesType extends RemoteModel
{
    protected $table = 'expenses_type';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Expenses_Type',
        'Expenses_Remark',
    ];

    protected $casts = [
        'ID' => 'integer',
        'Expenses_Type' => 'string',
        'Expenses_Remark' => 'string',
    ];
}