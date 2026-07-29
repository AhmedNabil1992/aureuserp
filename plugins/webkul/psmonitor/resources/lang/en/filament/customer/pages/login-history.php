<?php

return [
    'title' => 'Login History',
    'table' => [
        'columns' => [
            'id'          => 'ID',
            'user_id'     => 'User ID',
            'user_name'   => 'User Name',
            'date'        => 'Date',
            'time'        => 'Time',
            'ip_address'  => 'IP Address',
            'remark'      => 'Remark',
        ],
        'filters' => [
            'from'  => 'From',
            'until' => 'Until',
        ],
        'empty_state' => [
            'heading'     => 'No Login History Found',
            'description' => 'No login records were found for the selected range.',
        ],
    ],
];
