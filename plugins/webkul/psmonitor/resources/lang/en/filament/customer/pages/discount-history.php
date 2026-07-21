<?php

return [
    'title' => 'Discount History',
    'navigation_group' => 'PlayStation',
    'table' => [
        'columns' => [
            'invoice_no' => 'Invoice No.',
            'amount' => 'Discount Amount',
            'reason' => 'Reason',
            'username' => 'User',
            'date' => 'Date',
            'time' => 'Time',
            'shift_no' => 'Shift No.',
        ],
        'filters' => [
            'from' => 'From Date',
            'until' => 'Until Date',
        ],
    ],
    'notifications' => [
        'connection_failed' => [
            'title' => 'Branch Database Connection Failed',
            'body' => 'The remote server for the selected branch is currently unreachable. Please check the shop connection or try again later.',
        ],
    ],
];
