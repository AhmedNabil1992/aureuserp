<?php

return [
    'navigation' => [
        'title' => 'Tags',
        'group' => 'Articles',
    ],

    'model-label'        => 'Tag',
    'plural-model-label' => 'Tags',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Tag Details',
                'fields' => [
                    'name'  => 'Name',
                    'color' => 'Color',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'color'          => 'Color',
            'name'           => 'Name',
            'articles-count' => 'Articles',
            'created-at'     => 'Created At',
        ],
    ],
];
