<?php

return [
    'navigation' => [
        'title' => 'Help Articles',
    ],

    'model-label'        => 'Article',
    'plural-model-label' => 'Articles',

    'table' => [
        'columns' => [
            'title'        => 'Title',
            'category'     => 'Category',
            'tags'         => 'Tags',
            'published-at' => 'Published At',
        ],
        'filters' => [
            'category' => 'Category',
        ],
    ],

    'infolist' => [
        'sections' => [
            'video' => [
                'title' => 'Video',
            ],
            'details' => [
                'title'   => 'Details',
                'entries' => [
                    'category'     => 'Category',
                    'tags'         => 'Tags',
                    'published-at' => 'Published At',
                ],
            ],
        ],
    ],

    'pages' => [
        'view' => [
            'actions' => [
                'back' => 'Back to Articles',
            ],
        ],
    ],
];
