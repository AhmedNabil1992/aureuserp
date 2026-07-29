<?php

return [
    'title' => 'Vault Transactions History',
    'table' => [
        'columns' => [
            'trx_date'  => 'Date',
            'trx_time'  => 'Time',
            'trx_type'  => 'Transaction Type',
            'trx_name'  => 'Transaction Name',
            'amount'    => 'Amount',
            'username'  => 'Username',
            'shift'     => 'Shift',
            'reference' => 'Reference',
        ],
        'filters' => [
            'from'      => 'From',
            'until'     => 'Until',
            'trx_type'  => 'Transaction Type',
            'trx_name'  => 'Transaction Name',
            'username'  => 'Username',
            'reference' => 'Reference',
        ],
        'summaries' => [
            'total_amount' => 'Total Transactions Amount',
        ],
        'empty_state' => [
            'heading'     => 'No Vault Transactions Found',
            'description' => 'No safe transactions were found for the selected range.',
        ],
    ],
];
