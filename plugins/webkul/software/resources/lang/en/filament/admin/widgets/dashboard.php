<?php

return [
    'stats' => [
        'heading' => 'Software Overview',

        'programs' => [
            'label'       => 'Programs',
            'description' => 'Total software programs',
        ],

        'active_licenses' => [
            'label'       => 'Active Licenses',
            'description' => 'Currently active licenses',
        ],

        'registered_devices' => [
            'label'       => 'Registered Devices',
            'description' => 'Devices linked to licenses',
        ],

        'open_tickets' => [
            'label'       => 'Open Tickets',
            'description' => 'Support tickets awaiting action',
        ],
    ],

    'license_chart' => [
        'heading'       => 'Licenses by Status',
        'dataset_label' => 'Licenses',
    ],

    'ticket_chart' => [
        'heading'       => 'Tickets by Status',
        'dataset_label' => 'Tickets',
    ],

    'subscription_chart' => [
        'heading'       => 'Subscriptions by Status',
        'dataset_label' => 'Subscriptions',

        'labels' => [
            'active'   => 'Active',
            'inactive' => 'Inactive',
            'expired'  => 'Expired',
        ],
    ],

    'subscription_types' => [
        'heading'       => 'Subscribers by Subscription Type',
        'dataset_label' => 'Subscribers',

        'labels' => [
            'unknown' => 'Unknown',
        ],
    ],

    'expiring_subscriptions' => [
        'heading' => 'Subscriptions Expiring This Month',

        'columns' => [
            'subscription_type' => 'Subscription Type',
            'expiring_count'    => 'Expiring This Month',
            'nearest_end_date'  => 'Nearest End Date',
        ],
    ],

    'subscription_alerts' => [
        'heading' => 'Subscription Alerts',

        'expiring_this_month' => [
            'label'       => 'Expiring This Month',
            'description' => 'Total subscriptions ending this month',
        ],

        'expiring_within_7_days' => [
            'label'       => 'Expiring In 7 Days',
            'description' => 'Subscriptions needing urgent follow-up',
        ],

        'expired_this_month' => [
            'label'       => 'Expired This Month',
            'description' => 'Subscriptions already expired since month start',
        ],
    ],

    'top_programs' => [
        'heading' => 'Top Programs by Licenses',

        'columns' => [
            'program'         => 'Program',
            'licenses'        => 'Licenses',
            'active_licenses' => 'Active Licenses',
            'tickets'         => 'Tickets',
        ],
    ],
];
