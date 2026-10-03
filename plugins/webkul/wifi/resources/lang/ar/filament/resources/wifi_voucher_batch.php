<?php

return [
    'navigation' => [
        'title' => 'دفعات الكروت',
        'group' => 'شبكة واي فاي',
    ],

    'model-label'        => 'دفعة كروت',
    'plural-model-label' => 'دفعات الكروت',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات الدفعة',
                'fields' => [
                    'wifi_purchase_id'         => 'الفاتورة',
                    'cloud_id'                 => 'السحابة',
                    'realm_id'                 => 'المجال',
                    'nasidentifier'            => 'نقطة الوصول (NAS Identifier)',
                    'profile_id'               => 'باقة الكروت',
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
                    'new_batch' => 'توليد الكروت',
                ],
            ],
        ],
    ],

    'messages' => [
        'generated_success' => 'تم توليد الكروت بنجاح.',
        'generated_warning' => 'تم حفظ الدفعة ولكن فشل توليد الكروت.',
    ],

    'table' => [
        'actions' => [
            'download_pdf' => 'تنزيل PDF',
        ],
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
