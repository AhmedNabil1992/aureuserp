<?php

return [
    'title' => 'Play Sessions History',
    'navigation_group' => 'Branch Monitor',
    'table' => [
        'columns' => [
            'trx_date' => 'Date',
            'invoice_no' => 'Invoice No.',
            'device_name' => 'Device / Room Name',
            'play_type' => 'Play Type',
            'hour_price' => 'Hour Rate',
            'play_time' => 'Duration (mins)',
            'cost' => 'Session Cost',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'username' => 'User',
            'shift_no' => 'Shift No.',
        ],
        'summaries' => [
            'total_minutes' => 'Total Minutes',
            'total_cost' => 'Total Revenue',
            'count' => 'Sessions Count',
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
