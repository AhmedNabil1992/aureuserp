<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company' => 'الشركة',
                'country' => 'البلد',
                'name' => 'الاسم',
                'preceding-subtotal' => 'الإجمالي السابق',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company' => 'الشركة',
            'country' => 'البلد',
            'created-by' => 'أنشئ بواسطة',
            'name' => 'الاسم',
            'preceding-subtotal' => 'الإجمالي السابق',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'آخر تعديل',
        ],

        'groups' => [
            'name' => 'الاسم',
            'company' => 'الشركة',
            'country' => 'البلد',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'آخر تعديل',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف مجموعة الضريبة',
                        'body' => 'تم حذف مجموعة الضريبة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف مجموعة الضريبة',
                        'body' => 'لا يمكن حذف مجموعة الضريبة لأنها مستخدمة حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف مجموعات الضريبة',
                        'body' => 'تم حذف مجموعات الضريبة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف مجموعات الضريبة',
                        'body' => 'لا يمكن حذف مجموعات الضريبة لأنها مستخدمة حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company' => 'الشركة',
                'country' => 'البلد',
                'name' => 'الاسم',
                'preceding-subtotal' => 'الإجمالي السابق',
            ],
        ],
    ],
];
