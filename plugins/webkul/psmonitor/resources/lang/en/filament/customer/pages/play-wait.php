<?php

return [
    'title' => 'Current Play Sessions',
    'table' => [
        'columns' => [
            'order_no'    => 'Order No',
            'device_name' => 'Device Name',
            'start_time'  => 'Start Time',
            'end_time'    => 'End Time',
            'period'      => 'Period',
            'cost'        => 'Cost',
            'play_type'   => 'Play Type',
            'play_price'  => 'Play Price',
            'user_name'   => 'Username',
            'shift'       => 'Shift',
        ],
        'actions' => [
            'device_current' => 'Current Log',
        ],
        'summaries' => [
            'total_cost' => 'Total Current Cost',
        ],
        'empty_state' => [
            'heading'     => 'No Active Play Sessions',
            'description' => 'There are no active gaming sessions currently running.',
        ],
    ],
];
