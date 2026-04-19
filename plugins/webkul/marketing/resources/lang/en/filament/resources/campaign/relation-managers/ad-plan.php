<?php

return [
    'title' => 'Ad Plan',

    'form' => [
        'sections' => [
            'planned' => [
                'title' => 'Planned (Start of Month)',
            ],
            'actual' => [
                'title' => 'Actual (End of Month)',
            ],
        ],
        'fields' => [
            'planned-budget'      => 'Planned Budget',
            'planned-reach'       => 'Planned Reach',
            'planned-messages'    => 'Planned Messages',
            'planned-conversions' => 'Planned Conversions',
            'actual-budget'       => 'Actual Budget',
            'actual-reach'        => 'Actual Reach',
            'actual-messages'     => 'Actual Messages',
            'actual-conversions'  => 'Actual Conversions',
            'actual-leads'        => 'Leads Generated',
            'notes'               => 'Notes',
        ],
    ],

    'table' => [
        'columns' => [
            'planned-budget'      => 'Planned Budget',
            'actual-budget'       => 'Actual Budget',
            'planned-messages'    => 'Planned Messages',
            'actual-messages'     => 'Actual Messages',
            'planned-conversions' => 'Planned Conversions',
            'actual-conversions'  => 'Actual Conversions',
            'actual-leads'        => 'Leads Generated',
        ],
    ],
];
