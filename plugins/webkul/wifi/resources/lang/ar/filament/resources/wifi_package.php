<?php

return [
    'navigation' => [
        'title' => 'حزم واي فاي',
        'group' => 'شبكة واي فاي',
    ],

    'model-label'        => 'حزمة واي فاي',
    'plural-model-label' => 'حزم واي فاي',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات الحزمة',
                'fields' => [
                    'product_id'               => 'منتج الخدمة',
                    'product_id_helper_text'   => 'مستحسن: اربط كل باقات الواي فاي بمنتج خدمة واحد (Wi-Fi Voucher).',
                    'package_type'             => 'نوع الحزمة',
                    'package_type_helper_text' => 'استخدم غير محدود للباقات المفتوحة، ومحدود للباقات المرتبطة بوقت.',
                    'currency_id'              => 'العملة',
                    'quantity'                 => 'عدد البطاقات لكل وحدة',
                    'amount'                   => 'سعر البيع',
                    'dealer_amount'            => 'سعر الموزع',
                    'is_active'                => 'نشط',
                    'description'              => 'الوصف',
                ],
            ],
        ],
        'buttons' => [
            'new-package' => 'إضافة حزمة واي فاي',
        ],
    ],

    'table' => [
        'columns' => [
            'product'        => 'منتج الخدمة',
            'package_type'   => 'نوع الحزمة',
            'currency'       => 'العملة',
            'quantity'       => 'عدد البطاقات',
            'amount'         => 'سعر البيع',
            'dealer_amount'  => 'سعر الموزع',
            'is_active'      => 'نشط',
            'updated_at'     => 'آخر تحديث',
        ],
    ],
];
