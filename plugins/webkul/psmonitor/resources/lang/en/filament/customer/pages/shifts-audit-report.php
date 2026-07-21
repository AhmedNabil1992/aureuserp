<?php

return [
    'title' => 'Shifts & Cash Audit Report',
    'navigation_group' => 'PlayStation',
    'table' => [
        'columns' => [
            'shift_no' => 'Shift No.',
            'shift_date' => 'Date',
            'shift_open' => 'Opened By',
            'shift_close' => 'Closed By',
            'start_amt' => 'Opening Cash',
            'playstation' => 'PlayStation Revenue',
            'sales_amt' => 'Buffet Sales',
            'expenses_amt' => 'Expenses',
            'remain_amt' => 'Expected Cash',
            'actual_amt' => 'Actual Cash',
            'different' => 'Difference',
            'status' => 'Status',
        ],
        'summaries' => [
            'total_playstation' => 'Total PlayStation Revenue',
            'total_sales' => 'Total Buffet Sales',
            'total_expenses' => 'Total Expenses',
        ],
        'filters' => [
            'from' => 'From Date',
            'until' => 'Until Date',
        ],
    ],
    'notifications' => [
        'connection_failed' => [
            'title' => 'Branch Database Connection Failed',
            'body' => 'The remote server for the selected branch is currently unreachable.',
        ],
    ],
];
