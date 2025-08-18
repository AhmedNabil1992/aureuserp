<?php

return [
    'global-search' => [
        'zip-from' => 'من الرمز البريدي',
        'zip-to' => 'إلى الرمز البريدي',
        'name' => 'الاسم',
    ],

    'form' => [
        'fields' => [
            'name' => 'الاسم',
            'foreign-vat' => 'ضريبة القيمة المضافة الأجنبية',
            'country' => 'البلد',
            'country-group' => 'مجموعة البلدان',
            'zip-from' => 'بدء الرمز البريدي',
            'zip-to' => 'نهاية الرمز البريدي',
            'detect-automatically' => 'كشف تلقائي',
            'notes' => 'ملاحظات',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'company' => 'الشركة',
            'country' => 'البلد',
            'country-group' => 'مجموعة البلدان',
            'created-by' => 'أنشأ بواسطة',
            'zip-from' => 'من الرمز البريدي',
            'zip-to' => 'إلى الرمز البريدي',
            'status' => 'الحالة',
            'detect-automatically' => 'كشف تلقائي',
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
                    'title' => 'تم حذف المركز الضريبي',
                    'body' => 'تم حذف المركز الضريبي بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'الاسم',
            'foreign-vat' => 'ضريبة القيمة المضافة الأجنبية',
            'country' => 'البلد',
            'country-group' => 'مجموعة البلدان',
            'zip-from' => 'من الرمز البريدي',
            'zip-to' => 'إلى الرمز البريدي',
            'detect-automatically' => 'كشف تلقائي',
            'notes' => 'ملاحظات',
        ],
    ],
];
