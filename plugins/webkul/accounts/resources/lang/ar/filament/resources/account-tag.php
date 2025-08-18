<?php

return [
    'form' => [
        'fields' => [
            'color' => 'اللون',
            'country' => 'البلد',
            'applicability' => 'الجهة التطبيقية',
            'name' => 'الاسم',
            'status' => 'الحالة',
            'tax-negate' => 'إلغاء الضريبة',
        ],
    ],

    'table' => [
        'columns' => [
            'color' => 'اللون',
            'country' => 'البلد',
            'created-by' => 'أنشأ بواسطة',
            'applicability' => 'الجهة التطبيقية',
            'name' => 'الاسم',
            'status' => 'الحالة',
            'tax-negate' => 'إلغاء الضريبة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
            'deleted-at' => 'تاريخ الحذف',
        ],

        'filters' => [
            'bank' => 'بنك',
            'account-holder' => 'صاحب الحساب',
            'creator' => 'المنشئ',
            'can-send-money' => 'يمكن إرسال الأموال',
        ],

        'groups' => [
            'country' => 'البلد',
            'created-by' => 'أنشأ بواسطة',
            'applicability' => 'الجهة التطبيقية',
            'name' => 'الاسم',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث وسم الحساب',
                    'body' => 'تم تحديث وسم الحساب بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف وسم الحساب',
                    'body' => 'تم حذف وسم الحساب بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف وسوم الحساب',
                    'body' => 'تم حذف وسوم الحساب بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color' => 'اللون',
            'country' => 'البلد',
            'applicability' => 'الجهة التطبيقية',
            'name' => 'الاسم',
            'status' => 'الحالة',
            'tax-negate' => 'إلغاء الضريبة',
        ],
    ],
];
