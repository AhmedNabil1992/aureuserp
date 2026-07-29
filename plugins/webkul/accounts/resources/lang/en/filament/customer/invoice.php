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

    'table' => [
        'columns' => [
            'invoice_number' => 'Invoice Number',
            'invoice_date'   => 'Invoice Date',
            'due_date'       => 'Due Date',
            'total'          => 'Total Amount',
            'amount_due'     => 'Amount Due',
            'status'         => 'Status',
            'payment_status' => 'Payment Status',
            'customer'       => 'Customer',
        ],
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
                'invoice_number' => 'Invoice Number',
                'product'        => 'Product',
                'quantity'       => 'Quantity',
                'unit_price'     => 'Unit Price',
                'subtotal'       => 'Subtotal',
            ],
            'entries' => [
                'invoice_lines' => 'Invoice Items',
            ],
        ],
    ],
];
