<?php

return [
    'navigation' => [
        'title' => 'Categories',
        'group' => 'Articles',
    ],

    'model-label'        => 'Category',
    'plural-model-label' => 'Categories',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Category Details',
                'fields' => [
                    'name'        => 'Name',
                    'slug'        => 'Slug',
                    'description' => 'Description',
                    'color'       => 'Color',
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
