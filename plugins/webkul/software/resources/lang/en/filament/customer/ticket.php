<?php

return [
    'navigation' => [
        'label' => 'Support Tickets',
    ],

    'models' => [
        'singular' => 'Support Ticket',
    ],

    'form' => [
        'fields' => [
            'license_or_product' => 'License / Product',
            'describe_issue' => 'Describe your issue',
            'attachments_optional' => 'Attachments (optional)',
        ],
    ],

    'table' => [
        'columns' => [
            'number' => '#',
            'product' => 'Product',
            'last_update' => 'Last Update',
        ],
    ],
];
