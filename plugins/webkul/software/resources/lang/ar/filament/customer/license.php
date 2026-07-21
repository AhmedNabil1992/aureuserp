<?php

return [
    'navigation' => [
        'label' => 'قائمة البرامج',
        'group' => 'الحساب',
    ],

    'models' => [
        'singular' => 'ترخيص برنامج',
        'plural'   => 'تراخيص البرامج',
    ],

    'table' => [
        'columns' => [
            'serial_number' => 'رقم السيريال',
            'program_name'  => 'اسم البرنامج',
            'edition'       => 'الإصدار',
            'status'        => 'الحالة',
            'start_date'    => 'تاريخ البداية',
            'end_date'      => 'تاريخ النهاية',
            'devices_count' => 'عدد الأجهزة',
        ],
        'filters' => [
            'status'  => 'الحالة',
            'program' => 'البرنامج',
        ],
    ],

    'pages' => [
        'list' => [
            'title' => 'تراخيص البرامج',
        ],
        'view' => [
            'title'  => 'تفاصيل الرخصة',
            'fields' => [
                'serial_number' => 'رقم السيريال',
                'program_name'  => 'اسم البرنامج',
                'edition'       => 'الإصدار',
                'status'        => 'الحالة',
                'start_date'    => 'تاريخ البداية',
                'end_date'      => 'تاريخ النهاية',
                'is_active'     => 'مفعل',
            ],
            'subscriptions' => [
                'title' => 'الاشتراكات والخدمات المفعلة',
                'columns' => [
                    'feature_name' => 'اسم الخدمة',
                    'service_type' => 'نوع الخدمة',
                    'start_date'   => 'تاريخ البداية',
                    'end_date'     => 'تاريخ النهاية',
                    'status'       => 'الحالة',
                ],
            ],
        ],
    ],

    'statuses' => [
        'active'    => 'نشطة',
        'inactive'  => 'غير نشطة',
        'expired'   => 'منتهية',
        'suspended' => 'معلقة',
    ],

    'common' => [
        'yes' => 'نعم',
        'no'  => 'لا',
    ],
];
