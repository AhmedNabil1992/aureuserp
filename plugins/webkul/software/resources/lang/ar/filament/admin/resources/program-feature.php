<?php

return [
    'navigation' => [
        'label' => 'مميزات البرامج',
    ],

    'form' => [
        'fields' => [
            'subscription_type' => 'نوع الاشتراك',
            'service_product' => 'منتج الخدمة',
        ],
        'helper_text' => [
            'subscription_type' => 'عند الفوترة، تنشئ هذه الميزة سطر فاتورة واشتراكًا من هذا النوع.',
            'service_product' => 'سطر منتج الخدمة الذي سيُضاف إلى الفاتورة.',
        ],
    ],

    'table' => [
        'columns' => [
            'program' => 'البرنامج',
            'subscription_type' => 'نوع الاشتراك',
            'service_product' => 'منتج الخدمة',
        ],
    ],
];
