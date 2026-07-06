<?php

return [
    'navigation' => [
        'label' => 'Tickets',
    ],

    'form' => [
        'fields' => [
            'ticket_number' => 'Ticket #',
            'assign_to' => 'Assign To',
            'customer' => 'Customer',
            'license' => 'License',
            'program' => 'Program',
            'description' => 'Description',
            'attachments' => 'Attachments',
        ],
    ],

    'table' => [
        'columns' => [
            'number' => '#',
            'customer' => 'Customer',
            'license' => 'License',
            'program' => 'Program',
            'assigned_to' => 'Assigned To',
            'last_update' => 'Last Update',
        ],
    ],
];
