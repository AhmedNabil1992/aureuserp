<?php

return [
    'navigation' => [
        'title' => 'مواقعي وأنظمتي',
    ],
    'models' => [
        'singular' => 'موقعي الأونلاين',
        'plural'   => 'مواقعي وأنظمتي',
    ],
    'columns' => [
        'number'     => 'رقم الموقع',
        'name'       => 'اسم الموقع',
        'system'     => 'المنصة',
        'plan'       => 'الباقة',
        'status'     => 'الحالة',
        'expires_at' => 'تاريخ الانتهاء',
        'url'        => 'رابط الموقع',
        'price'      => 'سعر الاشتراك',
        'auto_renew' => 'التجديد التلقائي',
    ],
    'sections' => [
        'website'      => 'بيانات الموقع',
        'subscription' => 'بيانات الاشتراك',
    ],
    'values' => [
        'enabled'  => 'مفعّل',
        'disabled' => 'غير مفعّل',
    ],
    'fields' => [
        'billing_cycle' => 'فترة التجديد',
    ],
    'actions' => [
        'visit'      => 'فتح الموقع / لوحة التحكم',
        'renew'      => 'تجديد الاشتراك من الرصيد',
        'create_new' => 'إنشاء موقع جديد',
    ],
    'notifications' => [
        'renewed_success' => 'تم تجديد الاشتراك بنجاح وخصم القيمة من الرصيد',
        'renew_failed'    => 'فشل تجديد الاشتراك',
    ],
];
