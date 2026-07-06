<?php

return [
    'navigation' => [
        'label' => 'البرامج',
    ],

    'form' => [
        'fields' => [
            'name' => 'الاسم',
            'slug' => 'الرابط المختصر',
            'base_service_product' => 'منتج الخدمة الأساسي',
            'description' => 'الوصف',
            'installation_notes' => 'ملاحظات التثبيت',
            'active' => 'نشط',
        ],
        'helper_text' => [
            'base_service_product' => 'المنتج الرئيسي لهذا البرنامج.',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'description' => 'الوصف',
            'slug' => 'الرابط المختصر',
            'base_product' => 'المنتج الأساسي',
            'installation_notes' => 'ملاحظات التثبيت',
            'creator' => 'المنشئ',
            'created_at' => 'تاريخ الإنشاء',
            'active' => 'نشط',
            'updated_at' => 'تاريخ التحديث',
        ],
    ],
];
