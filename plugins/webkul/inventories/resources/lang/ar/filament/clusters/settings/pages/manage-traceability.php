<?php

return [
    'title' => 'إدارة التتبع',

    'form' => [
        'enable-lots-serial-numbers' => 'الدفعات والأرقام التسلسلية',
        'enable-lots-serial-numbers-helper-text' => 'احصل على تتبع كامل من الموردين إلى العملاء',
        'configure-lots' => 'تهيئة الدفعات',
        'enable-expiration-dates' => 'تواريخ الانتهاء',
        'enable-expiration-dates-helper-text' => 'تعيين تواريخ انتهاء على الدفعات والأرقام التسلسلية',
        'display-on-delivery-slips' => 'عرض على إيصالات التسليم',
        'display-on-delivery-slips-helper-text' => 'ستظهر الدفعات والأرقام التسلسلية على إيصالات التسليم',
        'display-expiration-dates-on-delivery-slips' => 'عرض تواريخ الانتهاء على إيصالات التسليم',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'ستظهر تواريخ الانتهاء على إيصال التسليم',
        'enable-consignments' => 'الشحنات',
        'enable-consignments-helper-text' => 'تعيين مالك للمنتجات المخزنة',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'لديك منتجات في المخزون بها تتبع دفعة/رقم تسلسلي مفعل.',
                'body' => 'أوقف التتبع أولاً على جميع المنتجات قبل إيقاف هذا الإعداد.',
            ],
        ],
    ],
];
