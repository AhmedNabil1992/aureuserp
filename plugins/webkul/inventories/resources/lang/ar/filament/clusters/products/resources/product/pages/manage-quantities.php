<?php

return [
    'title' => 'الكميات',

    'tabs' => [
        'internal-locations' => 'المواقع الداخلية',
        'transit-locations' => 'مواقع النقل',
        'on-hand' => 'المتوفر',
        'to-count' => 'للحصر',
        'to-apply' => 'للتطبيق',
    ],

    'form' => [
        'fields' => [
            'product' => 'المنتج',
            'location' => 'الموقع',
            'package' => 'الطرد',
            'lot' => 'دفعة / أرقام تسلسلية',
            'on-hand-qty' => 'الكمية المتوفرة',
            'storage-category' => 'فئة التخزين',
        ],
    ],

    'table' => [
        'columns' => [
            'product' => 'المنتج',
            'location' => 'الموقع',
            'lot' => 'دفعة / أرقام تسلسلية',
            'storage-category' => 'فئة التخزين',
            'quantity' => 'الكمية',
            'package' => 'الطرد',
            'on-hand' => 'الكمية المتوفرة',
            'reserved-quantity' => 'الكمية المحجوزة',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'تم تحديث الكمية',
                    'body' => 'تم تحديث الكمية بنجاح.',
                ],
            ],
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
                        'body' => 'توجد كمية مسبقاً لنفس التكوين. يرجى تحديث الكمية بدلاً من ذلك.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الكمية',
                    'body' => 'تم حذف الكمية بنجاح.',
                ],
            ],
        ],
    ],
];
