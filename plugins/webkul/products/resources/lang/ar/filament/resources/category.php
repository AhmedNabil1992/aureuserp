<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name' => 'الاسم',
                    'name-placeholder' => 'مثال: مصابيح',
                    'parent' => 'الأب',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'full-name' => 'الاسم الكامل',
            'parent-path' => 'مسار الأب',
            'parent' => 'الأب',
            'creator' => 'المنشئ',
            'created-at' => 'تاريخ الإنشاء',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'parent' => 'الأب',
            'creator' => 'المنشئ',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'parent' => 'الأب',
            'creator' => 'المنشئ',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الفئة',
                        'body' => 'تم حذف الفئة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الفئة',
                        'body' => 'لا يمكن حذف الفئة لأنها قيد الاستخدام حاليًا.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الفئات',
                        'body' => 'تم حذف الفئات بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الفئات',
                        'body' => 'لا يمكن حذف الفئات لأنها قيد الاستخدام حاليًا.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'name' => 'الاسم',
                    'parent' => 'الفئة الأب',
                    'full_name' => 'الاسم الكامل للفئة',
                    'parent_path' => 'مسار الفئة',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'creator' => 'أنشئ بواسطة',
                    'created_at' => 'تاريخ الإنشاء',
                    'updated_at' => 'آخر تحديث في',
                ],
            ],
        ],
    ],
];
