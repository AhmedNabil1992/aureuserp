<?php

return [
    'navigation' => [
        'title' => 'كلاود العملاء',
        'group' => 'شبكة واي فاي',
    ],

    'model-label'        => 'كلاود العميل',
    'plural-model-label' => 'كلاود العملاء',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات الكلاود',
                'fields' => [
                    'partner_id' => 'العميل',
                    'cloud_id'   => 'الكلاود',
                ],
            ],
        ],
        'buttons' => [
            'new-mapping' => 'إضافة كلاود لعميل',
        ],
    ],

    'table' => [
        'columns' => [
            'id'           => 'المعرف',
            'partner'      => 'إسم العميل',
            'cloud'        => 'الكلاود',
            'cloud_number' => 'رقم الكلاود',
            'updated_at'   => 'آخر تحديث',
        ],
    ],
];
