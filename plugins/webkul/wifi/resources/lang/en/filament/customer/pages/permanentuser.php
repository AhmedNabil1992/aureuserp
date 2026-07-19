<?php
return [
    'title' => 'Permanent Users',
    'table' => [
        'columns' => [
            'cloud' => 'Cloud',
            'realm' => 'Group',
            'username' => 'Username',
            'profile' => 'Profile',
            'last_accept_time' => 'Last Accept Time',
            'last_reject_time' => 'Last Reject Time',
            'last_accept_nas' => 'Last Accept NAS',
            'last_reject_nas' => 'Last Reject NAS',
            'last_reject_message' => 'Last Reject Message',
            'created' => 'Created',
            'modified' => 'Modified',
            'active' => 'Active',
        ],
    ],
    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],
    'notifications' => [
        'delete' => [
            'success' => 'Permanent user deleted successfully.',
            'error' => 'An error occurred while deleting the permanent user.',
        ],
    ],
    'headeractions' => [
        'label' => 'Add New User',
        'create' => 'Create New Permanent User',
        'form' => [
            'cloud' => 'Cloud',
            'realm' => 'Group',
            'username' => 'Username',
            'password' => 'Password',
            'profile' => 'Profile',
            'username_helper' => 'Must be alphanumeric, at least 5 characters and no more than 20 characters',
            'password_helper' => 'Must be alphanumeric, at least 5 characters and no more than 20 characters',
        ],
        'notifications' => [
            'create' => [
                'success' => 'Permanent user created successfully.',
                'error' => 'An error occurred while creating the permanent user.',
            ],
        ],
    ],
];