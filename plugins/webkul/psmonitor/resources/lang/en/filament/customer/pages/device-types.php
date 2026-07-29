<?php

return [
    'title' => 'Device Types',
    'table' => [
        'columns' => [
            'id'          => 'ID',
            'name'        => 'Type Name',
            'description' => 'Description',
            'is_active'   => 'Active',
        ],
        'filters' => [
            'active_only' => 'Active Only',
        ],
        'actions' => [
            'add_play_type' => 'Add Play Type',
        ],
        'empty_state' => [
            'heading' => 'No device types found',
        ],
    ],
];
