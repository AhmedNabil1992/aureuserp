<?php

return [
    'navigation' => [
        'label' => 'التراخيص',
    ],

    'table' => [
        'columns' => [
            'program' => 'البرنامج',
            'edition' => 'الإصدار',
            'partner' => 'العميل',
            'partner_phone' => 'هاتف العميل',
            'state' => 'الولاية',
            'city' => 'المدينة',
            'approver' => 'المعتمد',
        ],
    ],

    'actions' => [
        'bill_license' => 'إصدار فاتورة الترخيص',
        'renew' => 'تجديد',
        'activate' => 'تفعيل',
        'deactivate' => 'إيقاف',
        'expire' => 'إنهاء',
        'edition' => 'الإصدار',
        'type' => 'النوع',
    ],

    'notifications' => [
        'trial_activated' => 'تم تفعيل الترخيص التجريبي بنجاح',
        'trial_expires_on' => 'ينتهي الترخيص التجريبي في :date',
        'invoice_created' => 'تم إنشاء الفاتورة بنجاح',
        'invoice_number' => 'رقم الفاتورة: :number',
        'invoice_failed' => 'فشل إنشاء الفاتورة',
        'renew_success' => 'تم تجديد الترخيص بنجاح',
        'renew_failed' => 'فشل تجديد الترخيص',
        'activate_success' => 'تم تفعيل الترخيص بنجاح',
        'activate_failed' => 'فشل تفعيل الترخيص',
        'deactivate_success' => 'تم إيقاف الترخيص بنجاح',
        'deactivate_failed' => 'فشل إيقاف الترخيص',
        'expire_success' => 'تم إنهاء الترخيص بنجاح',
        'expire_failed' => 'فشل إنهاء الترخيص',
    ],
];
