<?php

return [
    'navigation' => [
        'title' => 'التصنيفات',
        'group' => 'المقالات',
    ],

    'model-label'        => 'تصنيف',
    'plural-model-label' => 'التصنيفات',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'تفاصيل التصنيف',
                'fields' => [
                    'name'        => 'الاسم',
                    'slug'        => 'الرابط المختصر',
                    'description' => 'الوصف',
                    'color'       => 'اللون',
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
