<?php

return [
    'navigation' => [
        'title' => 'Permanent Users',
    ],

    'model-label'        => 'Permanent User',
    'plural-model-label' => 'Permanent Users',

    'actions' => [
        'create' => 'Create Permanent User',
        'delete' => 'Delete User',
    ],

    'form' => [
        'fields' => [
            'username'   => 'Username',
            'password'   => 'Password',
            'cloud_id'   => 'Cloud',
            'realm'      => 'Realm',
            'profile_id' => 'Package',
        ],
        'helpers' => [
            'username' => 'Must contain letters and numbers only, and be between 5 and 20 characters.',
            'password' => 'Must contain letters and numbers only, and be between 5 and 20 characters.',
        ],
    ],

    'messages' => [
        'created_success' => 'Permanent user created successfully.',
        'created_failed'  => 'Failed to create permanent user.',
        'deleted_success' => 'Permanent user deleted successfully.',
        'deleted_failed'  => 'Failed to delete permanent user.',
        'api_missing'     => 'Permanent users API configuration is incomplete.',
        'invalid_data'    => 'The selected cloud, realm, or package is invalid.',
    ],

    'table' => [
        'columns' => [
            'id'       => 'ID',
            'name'     => 'Name',
            'cloud'    => 'Cloud',
            'realm'    => 'Realm',
            'profile'  => 'Profile',
            'active'   => 'Active',
            'created'  => 'Created',
            'modified' => 'Modified',
        ],
        'filters' => [
            'cloud_id' => 'Cloud',
        ],
    ],
];
