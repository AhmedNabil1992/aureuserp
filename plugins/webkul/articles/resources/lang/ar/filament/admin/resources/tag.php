<?php

return [
    'navigation' => [
        'title' => 'الوسوم',
        'group' => 'المقالات',
    ],

    'model-label'        => 'وسم',
    'plural-model-label' => 'الوسوم',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'تفاصيل الوسم',
                'fields' => [
                    'name'  => 'الاسم',
                    'color' => 'اللون',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'color'          => 'اللون',
            'name'           => 'الاسم',
            'articles-count' => 'المقالات',
            'created-at'     => 'تاريخ الإنشاء',
        ],
    ],
];
