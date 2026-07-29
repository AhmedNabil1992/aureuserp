<?php

return [
    'title' => 'Sales & Invoices Report',
    'navigation_group' => 'PlayStation',
    'table' => [
        'columns' => [
            'date'       => 'Invoice Date',
            'invoice_no' => 'Invoice No',
            'amount'     => 'Sales Amount',
            'discount'   => 'Discount',
            'services'   => 'Services',
            'tax'        => 'Tax',
            'total'      => 'Net Total',
            'username'   => 'Username',
            'shift_no'   => 'Shift No',
        ],
        'actions' => [
            'details' => 'Details',
        ],
        'summaries' => [
            'total_amount'   => 'Total Sales',
            'total_discount' => 'Total Discounts',
            'total_services' => 'Total Services',
            'grand_total'    => 'Grand Net Total',
        ],
        'filters' => [
            'from'  => 'From Date',
            'until' => 'To Date',
        ],
    ],
];
