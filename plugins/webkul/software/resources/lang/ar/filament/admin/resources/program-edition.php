<?php

return [
    'navigation' => [
        'label' => 'إصدارات البرامج',
    ],

    'form' => [
        'fields' => [
            'linked_variant' => 'المتغير المرتبط (مطلوب للفوترة)',
        ],
        'feature_rules' => [
            'title'      => 'خصائص النسخة',
            'add_action' => 'إضافة قاعدة Feature',
            'fields'     => [
                'feature'                      => 'الخدمة',
                'price'                        => 'سعر مخصص',
                'auto_attach_on_final_license' => 'إضافة تلقائية عند الترخيص النهائي',
                'is_complimentary'             => 'هدية عند أول ترخيص',
                'invoice_on_initial_billing'   => 'فوترتها مع أول فاتورة',
                'invoice_on_renewal'           => 'فوترتها عند التجديد',
                'auto_renew_with_license'      => 'تجديدها تلقائيًا مع الترخيص',
            ],
            'helper_text' => [
                'price' => 'اتركه فارغًا لاستخدام سعر منتج الخدمة المرتبط.',
            ],
        ],
        'helper_text' => [
            'linked_variant' => 'اختر متغير المنتج الذي يمثل هذا الإصدار بدقة.',
        ],
    ],

    'table' => [
        'columns' => [
            'program'         => 'البرنامج',
            'name'            => 'الاسم',
            'variant'         => 'المتغير',
            'max_devices'     => 'أقصى عدد للأجهزة',
            'license_price'   => 'سعر الترخيص',
            'license_cost'    => 'تكلفة الترخيص',
            'monthly_renewal' => 'التجديد الشهري',
            'annual_renewal'  => 'التجديد السنوي',
            'created_at'      => 'تاريخ الإنشاء',
            'updated_at'      => 'تاريخ التحديث',
        ],
    ],
];
