<?php

return [
    'navigation' => [
        'label' => 'Program List',
        'group' => 'Account',
    ],

    'models' => [
        'singular' => 'Program License',
        'plural'   => 'Program Licenses',
    ],

    'table' => [
        'columns' => [
            'serial_number' => 'Serial Number',
            'program_name'  => 'Program Name',
            'edition'       => 'Edition',
            'status'        => 'Status',
            'start_date'    => 'Start Date',
            'end_date'      => 'End Date',
            'devices_count' => 'Devices Count',
        ],
        'filters' => [
            'status'  => 'Status',
            'program' => 'Program',
        ],
    ],

    'pages' => [
        'list' => [
            'title' => 'Program Licenses',
        ],
        'view' => [
            'title'  => 'License Details',
            'fields' => [
                'serial_number' => 'Serial Number',
                'program_name'  => 'Program Name',
                'edition'       => 'Edition',
                'status'        => 'Status',
                'start_date'    => 'Start Date',
                'end_date'      => 'End Date',
                'is_active'     => 'Active',
            ],
            'subscriptions' => [
                'title' => 'Active Subscriptions',
                'columns' => [
                    'feature_name' => 'Service Name',
                    'service_type' => 'Service Type',
                    'start_date'   => 'Start Date',
                    'end_date'     => 'End Date',
                    'status'       => 'Status',
                ],
            ],
        ],
    ],

    'statuses' => [
        'active'    => 'Active',
        'inactive'  => 'Inactive',
        'expired'   => 'Expired',
        'suspended' => 'Suspended',
    ],

    'common' => [
        'yes' => 'Yes',
        'no'  => 'No',
    ],
];
