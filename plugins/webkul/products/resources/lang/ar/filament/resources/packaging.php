<?php

return [
    'form' => [
        'name' => 'الإسم',
        'barcode' => 'الباركود',
        'product' => 'المنتج',
        'routes' => 'المسارات',
        'qty' => 'الكمية',
        'company' => 'الشركة',
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'product' => 'المنتج',
            'routes' => 'المسارات',
            'qty' => 'الكمية',
            'company' => 'الشركة',
            'barcode' => 'الباركود',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'product' => 'المنتج',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'product' => 'المنتج',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث التعبئة',
                    'body' => 'تم تحديث التعبئة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف التعبئة',
                        'body' => 'تم حذف التعبئة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف التعبئة',
                        'body' => 'لا يمكن حذف التعبئة لأنها قيد الاستخدام حاليًا.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'طباعة',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف عناصر التعبئة',
                        'body' => 'تم حذف عناصر التعبئة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف عناصر التعبئة',
                        'body' => 'لا يمكن حذف عناصر التعبئة لأنها قيد الاستخدام حاليًا.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'تعبئة جديدة',

                'notification' => [
                    'title' => 'تم إنشاء التعبئة',
                    'body' => 'تم إنشاء التعبئة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'name' => 'اسم التعبئة',
                    'barcode' => 'الباركود',
                    'product' => 'المنتج',
                    'qty' => 'الكمية',
                ],
            ],

            'organization' => [
                'title' => 'تفاصيل المنظمة',

                'entries' => [
                    'company' => 'الشركة',
                    'creator' => 'أنشأ بواسطة',
                    'created_at' => 'تاريخ الإنشاء',
                    'updated_at' => 'تاريخ التحديث',
                ],
            ],
        ],
    ],
];
