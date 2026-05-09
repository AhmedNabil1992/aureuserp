<?php

return [
    'title' => 'Balance History',
    'heading' => 'Balance Request History',

    'navigation' => [
        'label' => 'Balance History',
        'group' => 'Account',
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'amount' => 'Amount',
            'status' => 'Status',
            'request_date' => 'Request Date',
            'approval_date' => 'Approval Date',
            'notes' => 'Notes',
        ],
        'filters' => [
            'status' => 'Filter by Status',
        ],
        'actions' => [
            'view' => 'View Details',
        ],
    ],

    'statuses' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'processing' => 'Processing',
        'completed' => 'Completed',
    ],

    'form' => [
        'section' => [
            'details' => 'Details',
        ],
        'fields' => [
            'amount' => 'Amount',
            'status' => 'Status',
            'request_date' => 'Request Date',
            'approval_date' => 'Approval Date',
            'notes' => 'Notes',
        ],
    ],

    'notifications' => [
        'pending' => 'Your balance request is pending approval',
        'approved' => 'Your balance request has been approved',
        'rejected' => 'Your balance request has been rejected',
    ],
];
