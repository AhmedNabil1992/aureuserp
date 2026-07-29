<?php

return [
    'title' => 'Device Settings',
    'table' => [
        'columns' => [
            'device_name' => 'Device Name',
            'device_type' => 'Device Type',
            'kind'        => 'Operation Mode',
            'ip_address'  => 'IP Address',
            'status'      => 'Status',
            'limit_time'  => 'Min Time Limit',
            'is_active'   => 'Active',
        ],
        'filters' => [
            'is_active' => 'Active Only',
        ],
        'actions' => [
            'edit' => 'Edit',
        ],
        'empty_state' => [
            'heading' => 'No devices found',
        ],
    ],
];
