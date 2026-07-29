<?php

return [
    'title' => 'Inventory Stock',
    'table' => [
        'columns' => [
            'category'  => 'Category',
            'barcode'   => 'Item Code / Barcode',
            'item_name' => 'Item Name',
            'quantity'  => 'Quantity',
            'min_alert' => 'Minimum Alert',
        ],
        'filters' => [
            'low_stock'      => 'Low Stock Alert',
            'quantity_range' => 'Quantity Filter',
            'ranges' => [
                'zero'     => 'Zero',
                'positive' => 'Greater than zero',
                'negative' => 'Less than zero',
            ],
        ],
        'empty_state' => [
            'heading'     => 'No Stock Items Found',
            'description' => 'No inventory stock data is available for display.',
        ],
    ],
];
