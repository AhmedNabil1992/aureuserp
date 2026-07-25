<?php

return [
    'title'            => 'Internet Usage Summary',
    'navigation_label' => 'Usage Summary',
    'empty'            => [
        'heading'     => 'No Usage Data Available',
        'description' => 'No internet usage sessions found for the selected period.',
    ],
    'tabs' => [
        'all'     => 'All',
        'voucher' => 'Vouchers',
        'user'    => 'Permanent Users',
    ],
    'columns' => [
        'username'              => 'Username / Voucher',
        'type'                  => 'Type',
        'sessions_count'        => 'Sessions Count',
        'total_session_seconds' => 'Total Duration',
        'total_input_octets'    => 'Download',
        'total_output_octets'   => 'Upload',
        'total_octets'          => 'Total Data',
        'last_session_at'       => 'Last Session',
    ],
    'filters' => [
        'period'     => 'Filter by Period',
        'start_date' => 'Start Date',
        'end_date'   => 'End Date',
    ],
    'units' => [
        'hours'   => 'hrs',
        'minutes' => 'mins',
    ],
];