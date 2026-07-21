<?php

return [
    'title' => 'Sales & Invoices Report',
    'navigation_group' => 'Branch Monitor',
    'table' => [
        'columns' => [
            'date' => 'Invoice Date',
            'invoice_no' => 'Invoice No.',
            'amount' => 'Gross Amount',
            'discount' => 'Discount',
            'services' => 'Services',
            'tax' => 'Tax',
            'total' => 'Net Total',
            'username' => 'User',
            'shift_no' => 'Shift No.',
        ],
        'summaries' => [
            'total_amount' => 'Total Gross Sales',
            'total_discount' => 'Total Discounts',
            'total_services' => 'Total Services',
            'grand_total' => 'Grand Net Total',
        ],
        'filters' => [
            'from' => 'From Date',
            'until' => 'Until Date',
        ],
    ],
    'notifications' => [
        'connection_failed' => [
            'title' => 'Branch Database Connection Failed',
            'body' => 'The remote server for the selected branch is currently unreachable.',
        ],
    ],
];
