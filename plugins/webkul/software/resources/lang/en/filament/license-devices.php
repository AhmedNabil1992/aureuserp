<?php

return [
    'actions' => [
        'group_label'  => 'Actions',
        'generate_key' => [
            'label' => 'Generate Key',
        ],
        'cancel_key' => [
            'label' => 'Cancel Key',
        ],
        'delete' => [
            'label' => 'Delete',
        ],
    ],
    'notifications' => [
        'license_not_active' => [
            'title' => 'License is not active',
            'body'  => 'Activate the license first, then generate key.',
        ],
        'key_exists' => [
            'title' => 'Key already exists',
            'body'  => 'This device already has a generated key.',
        ],
        'device_limit_exceeded' => [
            'title' => 'Device limit exceeded',
            'body'  => 'Allowed devices limit has been exceeded for this edition.',
        ],
        'primary_conflict' => [
            'title' => 'Primary device conflict',
            'body'  => 'Only one primary device is allowed per license.',
        ],
        'generate_success' => [
            'title' => 'Key generated',
            'body'  => 'License key has been generated successfully.',
        ],
        'generate_failed' => [
            'title'     => 'Failed to generate key',
            'body'      => 'Unable to generate key now. Please try again.',
            'empty_key' => 'Generator response did not include a key.',
        ],
        'cancel_not_needed' => [
            'title' => 'No key to cancel',
            'body'  => 'This device does not have an active key.',
        ],
        'cancel_success' => [
            'title' => 'Key canceled',
            'body'  => 'Device key has been cleared successfully.',
        ],
    ],
];
