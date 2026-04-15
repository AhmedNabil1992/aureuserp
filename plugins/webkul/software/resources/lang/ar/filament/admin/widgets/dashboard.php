<?php

return [
    'stats' => [
        'heading' => 'نظرة عامة على البرامج',

        'programs' => [
            'label'       => 'البرامج',
            'description' => 'إجمالي البرامج المسجلة',
        ],

        'active_licenses' => [
            'label'       => 'التراخيص النشطة',
            'description' => 'التراخيص المفعلة حاليًا',
        ],

        'registered_devices' => [
            'label'       => 'الأجهزة المسجلة',
            'description' => 'الأجهزة المرتبطة بالتراخيص',
        ],

        'open_tickets' => [
            'label'       => 'التذاكر المفتوحة',
            'description' => 'تذاكر الدعم التي تنتظر الإجراء',
        ],
    ],

    'license_chart' => [
        'heading'       => 'التراخيص حسب الحالة',
        'dataset_label' => 'التراخيص',
    ],

    'ticket_chart' => [
        'heading'       => 'التذاكر حسب الحالة',
        'dataset_label' => 'التذاكر',
    ],

    'subscription_chart' => [
        'heading'       => 'الاشتراكات حسب الحالة',
        'dataset_label' => 'الاشتراكات',

        'labels' => [
            'active'   => 'نشط',
            'inactive' => 'غير نشط',
            'expired'  => 'منتهي',
        ],
    ],

    'subscription_types' => [
        'heading'       => 'عدد المشتركين حسب نوع الاشتراك',
        'dataset_label' => 'عدد المشتركين',

        'labels' => [
            'unknown' => 'غير محدد',
        ],
    ],

    'expiring_subscriptions' => [
        'heading' => 'الاشتراكات التي تنتهي هذا الشهر',

        'columns' => [
            'subscription_type' => 'نوع الاشتراك',
            'expiring_count'    => 'عدد المنتهية هذا الشهر',
            'nearest_end_date'  => 'أقرب تاريخ انتهاء',
        ],
    ],

    'subscription_alerts' => [
        'heading' => 'تنبيهات الاشتراكات',

        'expiring_this_month' => [
            'label'       => 'تنتهي هذا الشهر',
            'description' => 'إجمالي الاشتراكات المنتهية هذا الشهر',
        ],

        'expiring_within_7_days' => [
            'label'       => 'تنتهي خلال 7 أيام',
            'description' => 'اشتراكات تحتاج متابعة عاجلة',
        ],

        'expired_this_month' => [
            'label'       => 'منتهية هذا الشهر',
            'description' => 'اشتراكات انتهت بالفعل منذ بداية الشهر',
        ],
    ],

    'top_programs' => [
        'heading' => 'أعلى البرامج حسب عدد التراخيص',

        'columns' => [
            'program'         => 'البرنامج',
            'licenses'        => 'التراخيص',
            'active_licenses' => 'التراخيص النشطة',
            'tickets'         => 'التذاكر',
        ],
    ],
];
