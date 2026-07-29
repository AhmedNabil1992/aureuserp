<?php

return [
    'navigation' => [
        'label' => 'Payment Requests',
    ],

    'models' => [
        'singular' => 'Payment Request',
        'plural'   => 'Payment Requests',
    ],

    'actions' => [
        'create' => 'Create Request',
        'cancel' => 'Cancel Request',
    ],

    'pages' => [
        'view' => [
            'sections' => [
                'request' => 'Payment Request Details',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'Reference No',
            'amount' => 'Amount',
            'date'   => 'Date',
            'state'  => 'Status',
            'memo'   => 'Memo / Notes',
        ],
    ],

    'form' => [
        'fields' => [
            'amount' => 'Requested Amount',
            'date'   => 'Request Date',
            'memo'   => 'Memo / Notes',
        ],
    ],

    'infolist' => [
        'fields' => [
            'name'           => 'Reference No',
            'amount'         => 'Amount',
            'date'           => 'Date',
            'state'          => 'Status',
            'journal'        => 'Journal / Bank',
            'payment_method' => 'Payment Method',
            'memo'           => 'Memo / Notes',
        ],
    ],

    'notifications' => [
        'created' => [
            'title' => 'Payment request submitted',
            'body'  => 'Your request was submitted and is waiting for admin approval.',
        ],
        'canceled' => [
            'title' => 'Payment request canceled',
            'body'  => 'Your request has been canceled successfully.',
        ],
    ],

    'validation' => [
        'partner_not_found'            => 'Your customer account could not be resolved.',
        'bank_journal_not_available'   => 'No bank journal is configured to receive your request yet.',
        'payment_method_not_available' => 'No inbound payment method is available for this request.',
    ],
];
