<?php

return [
    'title' => 'تقرير المبيعات والفواتير',
    'navigation_group' => 'مراقبة الفروع',
    'table' => [
        'columns' => [
            'date' => 'تاريخ الفاتورة',
            'invoice_no' => 'رقم الفاتورة',
            'amount' => 'إجمالي المبيعات',
            'discount' => 'الخصم',
            'services' => 'الخدمة',
            'tax' => 'الضريبة',
            'total' => 'الصافي النهائي',
            'username' => 'المستخدم',
            'shift_no' => 'رقم الشيفت',
        ],
        'summaries' => [
            'total_amount' => 'إجمالي المبيعات',
            'total_discount' => 'إجمالي الخصومات',
            'total_services' => 'إجمالي الخدمات',
            'grand_total' => 'الصافي الكلي',
        ],
        'filters' => [
            'from' => 'من تاريخ',
            'until' => 'إلى تاريخ',
        ],
    ],
    'notifications' => [
        'connection_failed' => [
            'title' => 'تعذر الاتصال بقاعدة بيانات الفرع',
            'body' => 'السيرفر الخاص بالفرع المختار غير متاح حالياً.',
        ],
    ],
];
