<?php

return [
    'title' => 'الأوضاع الضريبية',

    'navigation' => [
        'title' => 'الأوضاع الضريبية',
        'group' => 'المحاسبة',
    ],

    'global-search' => [
        'zip-from' => 'الرمز البريدي من',
        'zip-to' => 'الرمز البريدي إلى',
        'name' => 'الاسم',
    ],

    'form' => [
        'fields' => [
            'name' => 'الاسم',
            'foreign-vat' => 'ضريبة القيمة المضافة الأجنبية',
            'country' => 'الدولة',
            'country-group' => 'مجموعة الدول',
            'zip-from' => 'الرمز البريدي من',
            'zip-to' => 'الرمز البريدي إلى',
            'detect-automatically' => 'اكتشاف تلقائي',
            'notes' => 'ملاحظات',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'company' => 'الشركة',
            'country' => 'الدولة',
            'country-group' => 'مجموعة الدول',
            'created-by' => 'أنشئ بواسطة',
            'zip-from' => 'الرمز البريدي من',
            'zip-to' => 'الرمز البريدي إلى',
            'status' => 'الحالة',
            'detect-automatically' => 'اكتشاف تلقائي',
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
                    'title' => 'تم حذف الوضع الضريبي',
                    'body' => 'تم حذف الوضع الضريبي بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'الاسم',
            'foreign-vat' => 'ضريبة القيمة المضافة الأجنبية',
            'country' => 'الدولة',
            'country-group' => 'مجموعة الدول',
            'zip-from' => 'الرمز البريدي من',
            'zip-to' => 'الرمز البريدي إلى',
            'detect-automatically' => 'اكتشاف تلقائي',
            'notes' => 'ملاحظات',
        ],
    ],
];
