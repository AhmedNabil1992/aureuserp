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
            'company_name'  => 'Company Name',
            'city'          => 'City',
            'state'         => 'State',
            'address'       => 'Address',
            'license_plan'  => 'License Plan',
            'status'        => 'Status',
            'start_date'    => 'Start Date',
            'end_date'      => 'End Date',
            'version'       => 'Current Version',
            'devices_count' => 'Devices Count',
        ],
        'filters' => [
            'status'  => 'Status',
            'program' => 'Program',
        ],
        'actions' => [
            'renew_service' => [
                'label'   => 'Subscribe or renew service',
                'service' => 'Service',
                'notifications' => [
                    'success' => 'Service subscription renewed successfully',
                    'ends_on' => 'The subscription is active until :date.',
                    'failed'  => 'Unable to renew the service',
                ],
            ],
            'shift_emails' => [
                'label' => 'Shift closing emails',
                'modal' => [
                    'heading'     => 'Manage shift closing emails',
                    'description' => 'Add the email addresses that should receive shift closing reports for this license.',
                    'submit'      => 'Save emails',
                ],
                'form' => [
                    'emails' => [
                        'label' => 'Approved email addresses',
                        'add'   => 'Add another email',
                    ],
                    'email' => [
                        'label' => 'Email address',
                    ],
                ],
                'notifications' => [
                    'saved' => [
                        'title' => 'Shift closing emails saved successfully',
                    ],
                ],
                'validation' => [
                    'format'     => 'The email address format is invalid.',
                    'disposable' => 'Disposable or temporary email addresses are not allowed.',
                    'domain'     => 'The email domain (:domain) does not exist or has no MX records.',
                    'mailbox'    => 'The email address (:email) does not exist on the mail server.',
                    'valid'      => 'The email address is valid.',
                ],
            ],
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
                'company_name'  => 'Company Name',
                'edition'       => 'Edition',
                'state'         => 'State',
                'city'          => 'City',
                'address'       => 'Address',
                'license_plan'  => 'License Plan',
                'status'        => 'Status',
                'start_date'    => 'Start Date',
                'end_date'      => 'End Date',
                'version'       => 'Current Version',
                'is_active'     => 'Active',
            ],
            'subscriptions' => [
                'title'   => 'Active Subscriptions',
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
