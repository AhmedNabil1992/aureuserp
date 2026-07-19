<?php

return [
    'title' => 'Topups',
    'table' => [
        'columns' => [
            'cloud' => 'Cloud',
            'permanent_user' => 'Permanent User',
            'data' => 'Data',
            'time' => 'Time',
            'days_to_use' => 'Days to Use',
            'comment' => 'Comment',
            'created' => 'Created',
            'modified' => 'Modified',
        ],
    ],
    'actions' => [
        'title' => 'Add Topup',
        'modal_heading' => 'Add New Topup',
    ],
    'headeractions' => [
        'form' => [
            'cloud' => 'Cloud',
            'username' => 'Username',
            'type' => 'Type',
            'value' => 'Value',
            'data_unit' => 'Data Unit',
            'comment' => 'Comment',
        ],
    ],
    'notifications' => [
        'topup_success' => 'Topup added successfully',
        'topup_failed' => 'Failed to add Topup',
    ],
];