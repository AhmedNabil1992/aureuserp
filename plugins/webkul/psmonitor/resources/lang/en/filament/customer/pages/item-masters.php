<?php

return [
    'title' => 'Products & Items Master',
    'table' => [
        'columns' => [
            'group'           => 'Category / Group',
            'code'            => 'Item Code',
            'item_name'       => 'Item Name',
            'item_price'      => 'Item Price',
            'table_price'     => 'Table Price',
            'direct_price'    => 'Direct Price',
            'min_stock_alert' => 'Min Stock Alert',
            'is_product'      => 'Is Product',
            'is_sales'        => 'Is Sales',
            'is_active'       => 'Is Active',
        ],
        'actions' => [
            'add_item' => 'Add Item',
        ],
        'empty_state' => [
            'heading' => 'No items found',
        ],
    ],
];
