<?php

return [
    'navigation' => [
        'title' => 'Customer Clouds',
        'group' => 'Network',
    ],

    'model-label'        => 'Customer Cloud',
    'plural-model-label' => 'Customer Clouds',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Cloud Information',
                'fields' => [
                    'partner_id' => 'Customer',
                    'cloud_id'   => 'Cloud',
                ],
            ],
        ],
        'buttons' => [
            'new-mapping' => 'Add New Customer Cloud',
        ],
    ],

    'table' => [
        'columns' => [
            'id'           => 'ID',
            'partner'      => 'Customer',
            'cloud'        => 'Cloud',
            'cloud_number' => 'Cloud Number',
            'updated_at'   => 'Updated At',
        ],
    ],
];
