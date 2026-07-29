<?php

return [
    'title' => 'Market Sales History',
    'table' => [
        'columns' => [
            'trx_date'    => 'Date',
            'trx_time'    => 'Time',
            'invoice_no'  => 'Invoice No',
            'client_name' => 'Device Name',
            'item_name'   => 'Item Name',
            'quantity'    => 'Quantity',
            'price'       => 'Price',
            'amount'      => 'Total',
            'username'    => 'Username',
            'shift'       => 'Shift',
        ],
        'filters' => [
            'from'        => 'From',
            'until'       => 'Until',
            'invoice_no'  => 'Invoice No',
            'client_name' => 'Device Name',
            'username'    => 'Username',
            'item_id'     => 'Item ID',
        ],
        'summaries' => [
            'total_amount' => 'Total Sales',
        ],
        'empty_state' => [
            'heading'     => 'No Market Sales Found',
            'description' => 'No item sales were found in the selected range.',
        ],
    ],
];
