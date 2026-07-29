<?php

return [
    'title' => 'Expenses History Log',
    'table' => [
        'columns' => [
            'id'              => '#',
            'expenses_type'   => 'Expense Type',
            'expenses_remark' => 'Remark / Description',
            'expenses_amt'    => 'Amount',
            'username'        => 'Username',
            'shift'           => 'Shift',
            'trx_date'        => 'Date',
            'trx_time'        => 'Time',
        ],
        'summaries' => [
            'total' => 'Total Expenses',
        ],
        'filters' => [
            'from'  => 'From',
            'until' => 'Until',
        ],
        'actions' => [
            'add_expense'    => 'Add Expense',
            'delete_expense' => 'Delete',
        ],
        'empty_state' => [
            'heading' => 'No expenses found',
        ],
    ],
];
