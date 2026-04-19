<?php

return [
    'navigation' => [
        'title' => 'Leads',
        'group' => 'Leads',
    ],

    'model-label'        => 'Lead',
    'plural-model-label' => 'Leads',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Lead Information',
                'fields' => [
                    'name'         => 'Full Name',
                    'phone'        => 'Phone',
                    'email'        => 'Email',
                    'company-name' => 'Company Name',
                    'service-type' => 'Service of Interest',
                    'notes'        => 'Notes',
                ],
            ],
            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'status'      => 'Status',
                    'source'      => 'Source',
                    'temperature' => 'Temperature',
                    'assigned-to' => 'Assigned To',
                    'campaign'    => 'Campaign',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Name',
            'phone'              => 'Phone',
            'service-type'       => 'Service',
            'status'             => 'Status',
            'temperature'        => 'Temperature',
            'source'             => 'Source',
            'interactions-count' => 'Interactions',
            'next-follow-up'     => 'Next Follow-up',
            'assigned-to'        => 'Assigned To',
            'campaign'           => 'Campaign',
            'created-at'         => 'Created At',
        ],
        'filters' => [
            'status'      => 'Status',
            'source'      => 'Source',
            'temperature' => 'Temperature',
            'assigned-to' => 'Assigned To',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Lead Information',
                'entries' => [
                    'name'         => 'Full Name',
                    'phone'        => 'Phone',
                    'email'        => 'Email',
                    'company-name' => 'Company Name',
                    'service-type' => 'Service of Interest',
                    'notes'        => 'Notes',
                ],
            ],
            'settings' => [
                'title'   => 'Details',
                'entries' => [
                    'status'      => 'Status',
                    'temperature' => 'Temperature',
                    'source'      => 'Source',
                    'assigned-to' => 'Assigned To',
                    'campaign'    => 'Campaign',
                    'creator'     => 'Created By',
                    'created-at'  => 'Created At',
                ],
            ],
        ],
    ],
];
