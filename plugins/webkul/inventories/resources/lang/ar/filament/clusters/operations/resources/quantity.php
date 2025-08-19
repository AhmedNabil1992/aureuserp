<?php

return [
    'navigation' => [
        'title' => 'الكميات',
        'group' => 'التعديلات',
    ],

    'form' => [
        'fields' => [
            'location' => 'الموقع',
            'product' => 'المنتج',
            'package' => 'الطرد',
            'lot' => 'دفعة / أرقام تسلسلية',
            'counted-qty' => 'الكمية المحصورة',
            'scheduled-at' => 'مجدول في',
            'storage-category' => 'فئة التخزين',
        ],
    ],

    'table' => [
        'columns' => [
            'location' => 'الموقع',
            'product' => 'المنتج',
            'product-category' => 'فئة المنتج',
            'lot' => 'دفعة / أرقام تسلسلية',
            'storage-category' => 'فئة التخزين',
            'available-quantity' => 'الكمية المتوفرة',
            'quantity' => 'الكمية',
            'package' => 'الطرد',
            'last-counted-at' => 'آخر حصر',
            'on-hand' => 'الكمية المتوفرة',
            'counted' => 'الكمية المحصورة',
            'difference' => 'الفرق',
            'scheduled-at' => 'مجدول في',
            'user' => 'المستخدم',
            'company' => 'الشركة',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'تم تحديث الكمية',
                    'body' => 'تم تحديث الكمية بنجاح.',
                ],
            ],
        ],

        'groups' => [
            'product' => 'المنتج',
            'product-category' => 'فئة المنتج',
            'location' => 'الموقع',
            'storage-category' => 'فئة التخزين',
            'lot' => 'دفعة / أرقام تسلسلية',
            'company' => 'الشركة',
            'package' => 'الطرد',
        ],

        'filters' => [
            'product' => 'المنتج',
            'uom' => 'وحدة القياس',
            'product-category' => 'فئة المنتج',
            'location' => 'الموقع',
            'storage-category' => 'فئة التخزين',
            'lot' => 'دفعة / أرقام تسلسلية',
            'company' => 'الشركة',
            'package' => 'الطرد',
            'on-hand-quantity' => 'الكمية المتوفرة',
            'difference-quantity' => 'كمية الفرق',
            'incoming-at' => 'الوارد في',
            'scheduled-at' => 'مجدول في',
            'user' => 'المستخدم',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
            'creator' => 'المنشئ',
        ],

        'header-actions' => [
            'create' => [
                'label' => 'إضافة كمية',

                'notification' => [
                    'title' => 'تمت إضافة الكمية',
                    'body' => 'تمت إضافة الكمية بنجاح.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'الكمية موجودة مسبقاً',
                        'body' => 'توجد كمية مسبقاً لهذا التكوين. يرجى تحديث الكمية الحالية بدلاً من ذلك.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'تطبيق',

                'notification' => [
                    'title' => 'تم تطبيق تغييرات الكمية',
                    'body' => 'تم تطبيق تغييرات الكمية بنجاح.',
                ],
            ],

            'clear' => [
                'label' => 'مسح',

                'notification' => [
                    'title' => 'تم مسح تغييرات الكمية',
                    'body' => 'تم مسح تغييرات الكمية بنجاح.',
                ],
            ],
        ],
    ],
];
