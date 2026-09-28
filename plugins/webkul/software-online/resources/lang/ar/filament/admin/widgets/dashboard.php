<?php

return [
    'stats' => [
        'total' => [
            'label'       => 'إجمالي التينتس',
            'description' => 'جميع اشتراكات الأنظمة',
        ],
        'active' => [
            'label'       => 'التينتس النشطة',
            'description' => ':percentage% من الإجمالي',
        ],
        'awaiting' => [
            'label'       => 'في انتظار التجهيز',
            'description' => ':failed عمليات فاشلة تحتاج متابعة',
        ],
        'revenue' => [
            'label'       => 'إيراد الاشتراكات',
            'description' => 'إجمالي المعاملات المسددة',
        ],
    ],
    'revenue' => [
        'heading' => 'اتجاه الاشتراكات خلال آخر 12 شهرًا',
        'datasets'=> [
            'revenue'       => 'إيراد الاشتراكات',
            'subscriptions' => 'عدد الاشتراكات',
        ],
    ],
    'status' => [
        'heading' => 'توزيع التينتس حسب الحالة',
        'labels'  => [
            'active'    => 'نشط',
            'awaiting'  => 'في انتظار التجهيز',
            'suspended' => 'متوقف',
            'expired'   => 'منتهي الاشتراك',
            'failed'    => 'فشل أو قيد الحذف',
        ],
    ],
    'attention' => [
        'heading' => 'اشتراكات تحتاج متابعة',
        'columns' => [
            'instance'   => 'التينت',
            'customer'   => 'العميل',
            'status'     => 'الحالة',
            'expires_at' => 'تاريخ الانتهاء',
            'amount'     => 'المبلغ',
        ],
    ],
    'syncs' => [
        'heading' => 'آخر عمليات المزامنة',
        'columns' => [
            'instance'    => 'التينت',
            'event'       => 'نوع العملية',
            'status'      => 'الحالة',
            'occurred_at' => 'الوقت',
        ],
        'statuses' => [
            'received'  => 'مستلم',
            'processed' => 'نجحت',
            'failed'    => 'فشلت',
        ],
        'events' => [
            'tenant_provisioning_started' => 'بدء التجهيز',
            'tenant_provisioned'          => 'اكتمل التجهيز',
            'tenant_provisioning_failed'  => 'فشل التجهيز',
            'tenant_renewed'              => 'تجديد الاشتراك',
            'tenant_suspended'            => 'إيقاف التينت',
            'tenant_activated'            => 'تفعيل التينت',
            'tenant_deletion_started'     => 'بدء الحذف',
            'tenant_deleted'              => 'حذف التينت',
            'tenant_entitlements_updated' => 'تحديث الحدود',
        ],
    ],
];
