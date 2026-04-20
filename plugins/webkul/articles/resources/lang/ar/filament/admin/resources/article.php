<?php

return [
    'navigation' => [
        'title' => 'المقالات',
        'group' => 'المقالات',
    ],

    'model-label'        => 'مقال',
    'plural-model-label' => 'المقالات',

    'global-search' => [
        'category' => 'التصنيف',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'محتوى المقال',
                'fields' => [
                    'title'           => 'العنوان',
                    'slug'            => 'الرابط المختصر',
                    'summary'         => 'الملخص',
                    'content'         => 'المحتوى',
                    'video-embed-url' => 'رابط تضمين الفيديو',
                    'cover-image'     => 'الصورة الغلاف',
                    'files'           => 'الملفات القابلة للتحميل',
                ],
            ],
            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'category'           => 'التصنيف',
                    'tags'               => 'الوسوم',
                    'programs'           => 'مرئي للبرامج',
                    'programs-helper'    => 'اتركه فارغاً لجعله مرئياً لجميع العملاء (ما لم يكن داخلياً).',
                    'is-internal'        => 'داخلي (للمسؤولين فقط)',
                    'is-internal-helper' => 'المقالات الداخلية مرئية للمسؤولين فقط وليس للعملاء.',
                    'is-published'       => 'منشور',
                    'published-at'       => 'تاريخ النشر',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'العنوان',
            'category'     => 'التصنيف',
            'is-internal'  => 'داخلي',
            'is-published' => 'منشور',
            'published-at' => 'تاريخ النشر',
            'creator'      => 'أنشئ بواسطة',
            'created-at'   => 'تاريخ الإنشاء',
        ],
        'filters' => [
            'category'      => 'التصنيف',
            'internal-only' => 'داخلي فقط',
            'customer-only' => 'للعملاء فقط',
            'published'     => 'منشور',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'محتوى المقال',
                'entries' => [
                    'title'           => 'العنوان',
                    'summary'         => 'الملخص',
                    'content'         => 'المحتوى',
                    'video-embed-url' => 'رابط الفيديو',
                    'cover-image'     => 'الصورة الغلاف',
                ],
            ],
            'settings' => [
                'title'   => 'الإعدادات',
                'entries' => [
                    'category'     => 'التصنيف',
                    'tags'         => 'الوسوم',
                    'programs'     => 'البرامج المرتبطة',
                    'is-internal'  => 'داخلي',
                    'is-published' => 'منشور',
                    'published-at' => 'تاريخ النشر',
                    'creator'      => 'أنشئ بواسطة',
                ],
            ],
        ],
    ],
];
