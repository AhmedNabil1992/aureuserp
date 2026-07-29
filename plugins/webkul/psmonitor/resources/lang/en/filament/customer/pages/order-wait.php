<?php

return [
    'title' => 'Current Cafe Orders',
    'table' => [
        'columns' => [
            'order_no'    => 'Order No',
            'device_name' => 'Device Name',
            'item_name'   => 'Item Name',
            'quantity'    => 'Quantity',
            'price'       => 'Price',
            'amount'      => 'Total',
            'print'       => 'Printed',
            'order_by'    => 'Ordered By',
            'notes'       => 'Notes',
        ],
        'actions' => [
            'device_current' => 'Current Log',
        ],
        'summaries' => [
            'total_amount' => 'Total Orders Amount',
        ],
        'empty_state' => [
            'heading'     => 'No Pending Orders',
            'description' => 'There are no active cafe orders currently.',
        ],
    ],
];
