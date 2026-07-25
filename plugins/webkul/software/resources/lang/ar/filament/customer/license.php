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
            'company_name'  => 'اسم الشركة',
            'city'          => 'المدينة',
            'state'         => 'الولاية',
            'address'       => 'العنوان',
            'license_plan'  => 'خطة الرخصة',
            'status'        => 'الحالة',
            'start_date'    => 'تاريخ البداية',
            'end_date'      => 'تاريخ النهاية',
            'version'       => 'الإصدار الحالي',
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
                'company_name'  => 'اسم الشركة',
                'city'          => 'المدينة',
                'state'         => 'الولاية',
                'address'       => 'العنوان',
                'license_plan'  => 'خطة الرخصة',
                'edition'       => 'الإصدار',
                'status'        => 'الحالة',
                'start_date'    => 'تاريخ البداية',
                'end_date'      => 'تاريخ النهاية',
                'is_active'     => 'مفعل',
                'version'       => 'الإصدار الحالي',
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
