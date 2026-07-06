<?php

return [
    'navigation' => [
        'label' => 'تذاكر الدعم',
    ],

    'models' => [
        'singular' => 'تذكرة دعم',
    ],

    'form' => [
        'fields' => [
            'license_or_product' => 'الترخيص / المنتج',
            'describe_issue' => 'اشرح المشكلة',
            'attachments_optional' => 'المرفقات (اختياري)',
        ],
    ],

    'table' => [
        'columns' => [
            'number' => '#',
            'product' => 'المنتج',
            'last_update' => 'آخر تحديث',
        ],
    ],
];
