<?php

return [
    'title' => 'Software Licenses',
    'heading' => 'My Licenses',
    'single' => 'License',
    'plural' => 'Licenses',

    'navigation' => [
        'label' => 'Licenses',
        'group' => 'Account',
    ],

    'table' => [
        'columns' => [
            'id' => 'License ID',
            'product_name' => 'Product',
            'license_key' => 'License Key',
            'status' => 'Status',
            'expiry_date' => 'Expiry Date',
            'activation_count' => 'Activations',
            'max_activations' => 'Max Activations',
        ],
        'filters' => [
            'status' => 'Filter by Status',
        ],
        'actions' => [
            'view' => 'View Details',
            'download' => 'Download License',
            'activate' => 'Activate',
        ],
    ],

    'pages' => [
        'view' => [
            'title' => 'License Details',
            'heading' => 'License Information',
            'sections' => [
                'info' => 'License Information',
                'activations' => 'Activations',
                'support' => 'Support',
            ],
            'fields' => [
                'license_key' => 'License Key',
                'product' => 'Product',
                'status' => 'Status',
                'expiry_date' => 'Expiry Date',
                'activation_count' => 'Active Activations',
                'max_activations' => 'Maximum Activations',
                'support_until' => 'Support Until',
                'notes' => 'Notes',
            ],
            'actions' => [
                'renew' => 'Renew License',
                'upgrade' => 'Upgrade License',
                'download' => 'Download',
            ],
        ],
    ],

    'statuses' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'expired' => 'Expired',
        'suspended' => 'Suspended',
    ],

    'empty_state' => [
        'heading' => 'No Licenses',
        'description' => 'You don\'t have any licenses yet. Contact support to purchase a license.',
    ],
];
