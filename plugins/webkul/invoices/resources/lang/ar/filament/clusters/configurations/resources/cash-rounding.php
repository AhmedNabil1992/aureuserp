<?php

return [
    'title' => 'تقريب النقدية',

    'navigation' => [
        'title' => 'تقريب النقدية',
        'group' => 'الإدارة',
    ],

    'global-search' => [
        'name' => 'الاسم',
    ],

    'form' => [
        'fields' => [
            'name' => 'الاسم',
            'rounding-precision' => 'دقة التقريب',
            'rounding-strategy' => 'استراتيجية التقريب',
            'profit-account' => 'حساب الأرباح',
            'loss-account' => 'حساب الخسائر',
            'rounding-method' => 'طريقة التقريب',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'rounding-strategy' => 'استراتيجية التقريب',
            'rounding-method' => 'طريقة التقريب',
            'created-by' => 'أنشئ بواسطة',
            'profit-account' => 'حساب الأرباح',
            'loss-account' => 'حساب الخسائر',
        ],

        'groups' => [
            'name' => 'الاسم',
            'rounding-strategy' => 'استراتيجية التقريب',
            'rounding-method' => 'طريقة التقريب',
            'created-by' => 'أنشئ بواسطة',
            'profit-account' => 'حساب الأرباح',
            'loss-account' => 'حساب الخسائر',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف تقريب النقدية',
                    'body' => 'تم حذف تقريب النقدية بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف تقريب النقدية',
                    'body' => 'تم حذف تقريب النقدية بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'الاسم',
            'rounding-precision' => 'دقة التقريب',
            'rounding-strategy' => 'استراتيجية التقريب',
            'profit-account' => 'حساب الأرباح',
            'loss-account' => 'حساب الخسائر',
            'rounding-method' => 'طريقة التقريب',
        ],
    ],
];
