<?php

return [
    'navigation' => [
        'title' => 'Campaigns',
        'group' => 'Marketing',
    ],

    'model-label'        => 'Campaign',
    'plural-model-label' => 'Campaigns',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Campaign Details',
                'fields' => [
                    'name'        => 'Campaign Name',
                    'platform'    => 'Platform',
                    'month'       => 'Month',
                    'description' => 'Description',
                ],
            ],
            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'status'      => 'Status',
                    'assigned-to' => 'Assigned To',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Campaign Name',
            'platform'         => 'Platform',
            'month'            => 'Month',
            'status'           => 'Status',
            'planned-budget'   => 'Planned Budget',
            'actual-budget'    => 'Actual Budget',
            'planned-messages' => 'Planned Messages',
            'actual-messages'  => 'Actual Messages',
            'actual-leads'     => 'Ad Plan Leads',
            'leads-count'      => 'Actual Leads',
            'assigned-to'      => 'Assigned To',
            'created-at'       => 'Created At',
        ],
        'filters' => [
            'platform' => 'Platform',
            'status'   => 'Status',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Campaign Details',
                'entries' => [
                    'name'        => 'Campaign Name',
                    'platform'    => 'Platform',
                    'month'       => 'Month',
                    'description' => 'Description',
                ],
            ],
            'plan' => [
                'title'   => 'Ad Plan',
                'entries' => [
                    'planned-budget'      => 'Planned Budget',
                    'actual-budget'       => 'Actual Budget',
                    'planned-reach'       => 'Planned Reach',
                    'actual-reach'        => 'Actual Reach',
                    'planned-messages'    => 'Planned Messages',
                    'actual-messages'     => 'Actual Messages',
                    'planned-conversions' => 'Planned Conversions',
                    'actual-conversions'  => 'Actual Conversions',
                    'actual-leads'        => 'Leads Generated',
                    'notes'               => 'Notes',
                ],
            ],
            'settings' => [
                'title'   => 'Settings',
                'entries' => [
                    'status'      => 'Status',
                    'assigned-to' => 'Assigned To',
                    'creator'     => 'Created By',
                    'created-at'  => 'Created At',
                ],
            ],
        ],
    ],
];
