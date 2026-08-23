<?php

return [
    'navigation' => [
        'title' => 'Transactions & Subscriptions',
    ],
    'models' => [
        'singular' => 'Subscription Transaction',
        'plural'   => 'Subscription Transactions',
    ],
    'fields' => [
        'instance'      => 'Instance #',
        'customer'      => 'Customer',
        'type'          => 'Type',
        'billing_cycle' => 'Billing Cycle',
        'amount'        => 'Amount Paid',
        'period_start'  => 'Period Start',
        'period_end'    => 'Period End',
        'created_at'    => 'Payment Date',
    ],
];
