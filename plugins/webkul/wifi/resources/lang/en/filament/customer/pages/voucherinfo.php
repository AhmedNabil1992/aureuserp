<?php

return [
    'navigation' => [
        'title' => 'Voucher Info',
    ],
    'table' => [
        'columns' => [
            'cloud' => 'Cloud',
            'realm' => 'Group',
            'batch' => 'Batch',
            'name' => 'Voucher Number',
            'profile' => 'Profile',
            'status' => 'Status',
            'status_type' => [
                'new'      => 'New',
                'used'     => 'Used',
                'depleted' => 'Depleted',
                'expired'  => 'Expired',
            ],
            'perc_time_used' => 'Percentage of Time Used',
            'perc_data_used' => 'Percentage of Data Used',
            'last_accept_time' => 'Last Accept Time',
            'last_reject_time' => 'Last Reject Time',
            'last_accept_nas' => 'Last Accept NAS',
            'last_reject_nas' => 'Last Reject NAS',
            'last_reject_message' => 'Last Reject Message',
            'expires' => 'Expires',
            'time_valid' => 'Time Valid',
            'created' => 'Created',
            'modified' => 'Modified',
        ],
        'actions' => [
            'view' => 'View Consumption Details',
        ],
    ],
    'view' => [
        'title' => 'Voucher Consumption Record',
        'cancel' => 'Close',
        'no_record' => 'No consumption records or active connections for this voucher yet.',
        'table' => [
            'mac' => 'MAC Address',
            'start_time' => 'Start Time',
            'stop_time' => 'Stop Time',
            'session_time' => 'Session Time',
            'data_in' => 'Data In',
            'data_out' => 'Data Out',
        ]
    ],
];