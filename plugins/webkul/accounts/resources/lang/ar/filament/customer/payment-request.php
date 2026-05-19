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
        'create' => 'إنشاء طلب',
        'cancel' => 'إلغاء الطلب',
    ],

    'pages' => [
        'view' => [
            'sections' => [
                'request' => 'تفاصيل الطلب',
            ],
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
