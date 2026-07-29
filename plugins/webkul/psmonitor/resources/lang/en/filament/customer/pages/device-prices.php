<?php

return [
    'title' => 'Gaming Rates & Prices',
    'table' => [
        'columns' => [
            'device_type' => 'Device Type',
            'device_name' => 'Device Name',
            'game_type'   => 'Play Type',
            'hour_price'  => 'Hourly Rate',
            's_from'      => 'Starts From',
        ],
        'actions' => [
            'add_new_price' => 'Add New Price',
            'edit_price'    => 'Edit Rate',
        ],
        'empty_state' => [
            'heading' => 'No rates available',
        ],
    ],
];
