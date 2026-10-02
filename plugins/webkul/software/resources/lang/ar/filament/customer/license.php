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
        'actions' => [
            'renew_service' => [
                'label'   => 'الاشتراك أو تجديد خدمة',
                'service' => 'الخدمة',
                'notifications' => [
                    'success' => 'تم تجديد اشتراك الخدمة بنجاح',
                    'ends_on' => 'الاشتراك نشط حتى :date.',
                    'failed'  => 'تعذر تجديد الخدمة',
                ],
            ],
            'shift_emails' => [
                'label' => 'إيميلات تقفيل الشيفت',
                'modal' => [
                    'heading'     => 'إدارة إيميلات تقفيل الشيفت',
                    'description' => 'أضف عناوين البريد الإلكتروني التي تستقبل تقارير تقفيل الشيفت لهذا الترخيص.',
                    'submit'      => 'حفظ الإيميلات',
                ],
                'form' => [
                    'emails' => [
                        'label' => 'قائمة الإيميلات المعتمدة',
                        'add'   => 'إضافة إيميل جديد',
                    ],
                    'email' => [
                        'label' => 'عنوان البريد الإلكتروني',
                    ],
                ],
                'notifications' => [
                    'saved' => [
                        'title' => 'تم حفظ إيميلات تقفيل الشيفت بنجاح',
                    ],
                ],
                'validation' => [
                    'format'     => 'صيغة البريد الإلكتروني غير صحيحة.',
                    'disposable' => 'غير مسموح باستخدام إيميلات مؤقتة أو وهمية.',
                    'domain'     => 'نطاق البريد الإلكتروني (:domain) غير موجود أو لا يحتوي على سجلات MX.',
                    'mailbox'    => 'عنوان البريد الإلكتروني (:email) غير موجود على خادم البريد.',
                    'valid'      => 'عنوان البريد الإلكتروني صالح.',
                ],
            ],
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
                'title'   => 'الاشتراكات والخدمات المفعلة',
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
