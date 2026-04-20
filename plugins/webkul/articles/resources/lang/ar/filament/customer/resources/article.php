<?php

return [
    'navigation' => [
        'title' => 'مقالات المساعدة',
    ],

    'model-label'        => 'مقال',
    'plural-model-label' => 'المقالات',

    'table' => [
        'columns' => [
            'title'        => 'العنوان',
            'category'     => 'التصنيف',
            'tags'         => 'الوسوم',
            'published-at' => 'تاريخ النشر',
        ],
        'filters' => [
            'category' => 'التصنيف',
        ],
    ],

    'infolist' => [
        'sections' => [
            'video' => [
                'title' => 'الفيديو',
            ],
            'details' => [
                'title'   => 'التفاصيل',
                'entries' => [
                    'category'     => 'التصنيف',
                    'tags'         => 'الوسوم',
                    'published-at' => 'تاريخ النشر',
                ],
            ],
        ],
    ],

    'pages' => [
        'view' => [
            'actions' => [
                'back' => 'العودة إلى المقالات',
            ],
        ],
    ],
];
