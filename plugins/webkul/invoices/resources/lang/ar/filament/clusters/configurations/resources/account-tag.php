<?php

return [
    'title' => 'وسوم الحسابات',

    'navigation' => [
        'title' => 'وسوم الحسابات',
        'group' => 'المحاسبة',
    ],

    'global-search' => [
        'country' => 'الدولة',
        'name' => 'الاسم',
    ],

    'form' => [
        'fields' => [
            'color' => 'اللون',
            'country' => 'الدولة',
            'applicability' => 'القابلية',
            'name' => 'الاسم',
            'status' => 'الحالة',
            'tax-negate' => 'إلغاء الضريبة',
        ],
    ],

    'table' => [
        'columns' => [
            'color' => 'اللون',
            'country' => 'الدولة',
            'created-by' => 'أنشئ بواسطة',
            'applicability' => 'القابلية',
            'name' => 'الاسم',
            'status' => 'الحالة',
            'tax-negate' => 'إلغاء الضريبة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
            'deleted-at' => 'تاريخ الحذف',
        ],

        'filters' => [
            'bank' => 'البنك',
            'account-holder' => 'صاحب الحساب',
            'creator' => 'المنشئ',
            'can-send-money' => 'يمكن إرسال المال',
        ],

        'groups' => [
            'country' => 'الدولة',
            'created-by' => 'أنشئ بواسطة',
            'applicability' => 'القابلية',
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
                    'title' => 'تم حذف وسوم الحسابات',
                    'body' => 'تم حذف وسوم الحسابات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color' => 'اللون',
            'country' => 'الدولة',
            'applicability' => 'القابلية',
            'name' => 'الاسم',
            'status' => 'الحالة',
            'tax-negate' => 'إلغاء الضريبة',
        ],
    ],
];
