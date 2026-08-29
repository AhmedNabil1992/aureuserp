<?php

return [
    'model_label'   => 'رد سريع',
    'plural_label'  => 'الردود السريعة',
    'fields'        => [
        'title'        => 'عنوان الرد',
        'shortcut'     => 'الاختصار',
        'service_type' => 'الخدمة / النظام',
        'is_active'    => 'مفعّل',
        'content'      => 'نص الرد',
        'created_at'   => 'تاريخ الإضافة',
    ],
    'helpers'       => [
        'shortcut' => 'اختصار يمكنك كتابته في الشات لاقتراح هذا الرد (مثال: /welcome)',
    ],
    'placeholders'  => [
        'all_services' => 'جميع الخدمات',
    ],
    'tabs' => [
        'active'   => 'الردود النشطة',
        'inactive' => 'غير النشطة',
        'archived' => 'المؤرشفة',
    ],
];
