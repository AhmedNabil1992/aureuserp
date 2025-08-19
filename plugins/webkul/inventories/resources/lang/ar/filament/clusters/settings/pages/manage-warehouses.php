<?php

return [
    'title' => 'إدارة المستودعات',

    'form' => [
        'enable-locations' => 'المواقع',
        'enable-locations-helper-text' => 'تتبع موقع المنتج في المستودع الخاص بك',
        'configure-locations' => 'تهيئة المواقع',
        'enable-multi-steps-routes' => 'مسارات متعددة الخطوات',
        'enable-multi-steps-routes-helper-text' => 'استخدم المسارات الخاصة بك لإدارة نقل المنتجات بين المستودعات',
        'configure-routes' => 'تهيئة مسارات المستودع',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'لديك عدة مستودعات',
                'body' => 'لا يمكنك تعطيل المواقع المتعددة إذا كان لديك أكثر من مستودع واحد.',
            ],
        ],
    ],
];
