<?php

return [
    'title' => 'رخص البرمجيات',
    'heading' => 'رخصي',
    'single' => 'رخصة',
    'plural' => 'رخص',

    'navigation' => [
        'label' => 'الرخص',
        'group' => 'الحساب',
    ],

    'table' => [
        'columns' => [
            'id' => 'رقم الرخصة',
            'product_name' => 'المنتج',
            'license_key' => 'مفتاح الرخصة',
            'status' => 'الحالة',
            'expiry_date' => 'تاريخ الانتهاء',
            'activation_count' => 'التفعيلات',
            'max_activations' => 'الحد الأقصى للتفعيلات',
        ],
        'filters' => [
            'status' => 'تصفية حسب الحالة',
        ],
        'actions' => [
            'view' => 'عرض التفاصيل',
            'download' => 'تحميل الرخصة',
            'activate' => 'تفعيل',
        ],
    ],

    'pages' => [
        'view' => [
            'title' => 'تفاصيل الرخصة',
            'heading' => 'معلومات الرخصة',
            'sections' => [
                'info' => 'معلومات الرخصة',
                'activations' => 'التفعيلات',
                'support' => 'الدعم الفني',
            ],
            'fields' => [
                'license_key' => 'مفتاح الرخصة',
                'product' => 'المنتج',
                'status' => 'الحالة',
                'expiry_date' => 'تاريخ الانتهاء',
                'activation_count' => 'التفعيلات النشطة',
                'max_activations' => 'الحد الأقصى للتفعيلات',
                'support_until' => 'الدعم حتى',
                'notes' => 'الملاحظات',
            ],
            'actions' => [
                'renew' => 'تجديد الرخصة',
                'upgrade' => 'ترقية الرخصة',
                'download' => 'تحميل',
            ],
        ],
    ],

    'statuses' => [
        'active' => 'نشطة',
        'inactive' => 'غير نشطة',
        'expired' => 'منتهية',
        'suspended' => 'معلقة',
    ],

    'empty_state' => [
        'heading' => 'لا توجد رخص',
        'description' => 'ليس لديك أي رخص حتى الآن. تواصل مع الدعم الفني لشراء رخصة.',
    ],
];
