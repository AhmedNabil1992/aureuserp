<?php

return [
    'navigation' => [
        'title' => 'Vouchers',
    ],

    'model-label'        => 'Voucher',
    'plural-model-label' => 'Vouchers',

    'table' => [
        'columns' => [
            'id'       => 'ID',
            'name'     => 'Voucher Code',
            'cloud_id' => 'Cloud ID',
            'realm'    => 'Realm',
            'status'   => 'Status',
            'profile'  => 'Profile',
            'created'  => 'Created',
            'modified' => 'Modified',
        ],
        'filters' => [
            'cloud_id' => 'Cloud',
        ],
    ],
];
