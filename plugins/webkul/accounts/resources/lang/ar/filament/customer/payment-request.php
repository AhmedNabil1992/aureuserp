<?php

return [
    'navigation' => [
        'label' => 'طلبات الدفع',
    ],

    'models' => [
        'singular' => 'طلب دفع',
        'plural'   => 'طلبات الدفع',
    ],

    'actions' => [
        'create' => 'إنشاء طلب جديد',
        'cancel' => 'إلغاء الطلب',
    ],

    'pages' => [
        'view' => [
            'sections' => [
                'request' => 'تفاصيل طلب الدفع',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'رقم المرجع',
            'amount' => 'المبلغ',
            'date'   => 'التاريخ',
            'state'  => 'الحالة',
            'memo'   => 'البيان / الملاحظات',
        ],
    ],

    'form' => [
        'fields' => [
            'amount' => 'المبلغ المطلوب',
            'date'   => 'تاريخ الطلب',
            'memo'   => 'البيان / الملاحظات',
        ],
    ],

    'infolist' => [
        'fields' => [
            'name'           => 'رقم المرجع',
            'amount'         => 'المبلغ',
            'date'           => 'التاريخ',
            'state'          => 'الحالة',
            'journal'        => 'اليومية / البنك',
            'payment_method' => 'طريقة الدفع',
            'memo'           => 'البيان / الملاحظات',
        ],
    ],

    'notifications' => [
        'created' => [
            'title' => 'تم إرسال طلب الدفع',
            'body'  => 'تم إرسال طلبك وهو الآن بانتظار موافقة الإدارة.',
        ],
        'canceled' => [
            'title' => 'تم إلغاء طلب الدفع',
            'body'  => 'تم إلغاء طلبك بنجاح.',
        ],
    ],

    'validation' => [
        'partner_not_found'            => 'تعذر تحديد حساب العميل الحالي.',
        'bank_journal_not_available'   => 'لا توجد يومية بنكية مهيأة لاستقبال هذا الطلب حالياً.',
        'payment_method_not_available' => 'لا توجد طريقة دفع واردة متاحة لهذا الطلب.',
    ],
];
