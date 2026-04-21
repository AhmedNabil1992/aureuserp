<?php

return [
    'navigation' => [
        'title' => 'دفعات القسائم',
        'group' => 'شبكة واي فاي',
    ],

    'model-label'        => 'دفعة قسيمة',
    'plural-model-label' => 'دفعات القسائم',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات الدفعة',
                'fields' => [
                    'wifi_purchase_id'         => 'الفاتورة',
                    'cloud_id'                 => 'السحابة',
                    'realm_id'                 => 'المجال',
                    'nasidentifier'            => 'نقطة الوصول (NAS Identifier)',
                    'profile_id'               => 'الملف التعريفي',
                    'validity'                 => 'مدة الصلاحية',
                    'days_valid'               => 'أيام',
                    'hours_valid'              => 'ساعات',
                    'minutes_valid'            => 'دقائق',
                    'batch_code'               => 'رمز الدفعة',
                    'quantity'                 => 'الكمية',
                    'never_expire'             => 'لا تنتهي الصلاحية',
                    'never_expire_helper_text' => 'يتم تعبئته تلقائيًا بناء علي الفاتورة.',
                    'caption'                  => 'العنوان',
                ],
                'buttons' => [
                    'new_batch' => 'توليد القسائم',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'المعرف',
            'batch_code'      => 'رمز الدفعة',
            'customer'        => 'العميل',
            'service_product' => 'منتج الخدمة',
            'cloud'           => 'السحابة',
            'access_point'    => 'نقطة الوصول',
            'quantity'        => 'الكمية',
            'never_expire'    => 'لا تنتهي الصلاحية',
            'created_at'      => 'تاريخ الإنشاء',
            'purchase'        => 'الشراء',
            'updated_at'      => 'آخر تحديث',
        ],
    ],
];
