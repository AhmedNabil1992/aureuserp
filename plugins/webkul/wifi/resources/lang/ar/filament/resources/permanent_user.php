<?php

return [
    'navigation' => [
        'title' => 'Permanent Users',
    ],

    'model-label'        => 'Permanent User',
    'plural-model-label' => 'Permanent Users',

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
