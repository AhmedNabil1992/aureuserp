<?php

return [
    'navigation' => [
        'title'   => 'Clouds',
        'group'   => 'Network',
        'refresh' => 'Refresh',
    ],

    'model-label'        => 'Cloud',
    'plural-model-label' => 'Clouds',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Cloud Information',
                'fields' => [
                    'id'       => 'ID',
                    'name'     => 'Name',
                    'created'  => 'Created',
                    'modified' => 'Modified',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'       => 'ID',
            'name'     => 'Name',
            'created'  => 'Created',
            'modified' => 'Modified',
        ],
    ],
];
