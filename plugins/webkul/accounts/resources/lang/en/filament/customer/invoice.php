<?php

return [
    'navigation' => [
        'label' => 'My Invoices',
        'group' => 'Accounting',
    ],

    'models' => [
        'singular' => 'Invoice',
        'plural'   => 'My Invoices',
    ],

    'pages' => [
        'view' => [
            'sections' => [
                'details' => 'Invoice Details',
            ],
            'tabs' => [
                'invoice_lines' => 'Invoice Lines',
            ],
            'columns' => [
                'product'    => 'Product',
                'quantity'   => 'Quantity',
                'unit_price' => 'Unit Price',
                'subtotal'   => 'Subtotal',
            ],
            'entries' => [
                'invoice_lines' => 'Invoice Items',
            ],
        ],
    ],
];
