<?php

return [
    'title' => 'مجموعات الضرائب',

    'navigation' => [
        'title' => 'مجموعات الضرائب',
        'group' => 'المحاسبة',
    ],

    'global-search' => [
        'company-name' => 'اسم الشركة',
        'payment-term' => 'شرط الدفع',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'company' => 'الشركة',
                'country' => 'الدولة',
                'name' => 'الاسم',
                'preceding-subtotal' => 'المجموع الفرعي السابق',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company' => 'الشركة',
            'country' => 'الدولة',
            'created-by' => 'أنشئ بواسطة',
            'name' => 'الاسم',
            'preceding-subtotal' => 'المجموع الفرعي السابق',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'name' => 'الاسم',
            'company' => 'الشركة',
            'country' => 'الدولة',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شرط الدفع',
                    'body' => 'تم حذف شرط الدفع بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف مجموعات الضرائب',
                    'body' => 'تم حذف مجموعات الضرائب بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company' => 'الشركة',
                'country' => 'الدولة',
                'name' => 'الاسم',
                'preceding-subtotal' => 'المجموع الفرعي السابق',
            ],
        ],
    ],
];
