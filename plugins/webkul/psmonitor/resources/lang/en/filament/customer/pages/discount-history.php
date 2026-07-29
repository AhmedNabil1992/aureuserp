<?php

return [
    'title' => 'Discount History',
    'navigation_group' => 'PlayStation',
    'empty_state' => [
        'heading' => 'No Discounts Found',
        'description' => 'No discounts were found for the selected period.',
    ],
    'table' => [
        'columns' => [
            'invoice_no' => 'Invoice No',
            'amount'     => 'Discount Amount',
            'reason'     => 'Reason',
            'username'   => 'Username',
            'date'       => 'Date',
            'time'       => 'Time',
            'shift_no'   => 'Shift No',
        ],
        'filters' => [
            'from'  => 'From Date',
            'until' => 'To Date',
        ],
        'summaries' => [
            'total_amount' => 'Total Discounts',
        ],
    ],
];
