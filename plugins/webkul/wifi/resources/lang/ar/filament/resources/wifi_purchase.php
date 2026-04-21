<?php

return [
    'navigation' => [
        'title' => 'شراء واي فاي',
        'group' => 'شبكة واي فاي',
    ],

    'model-label'        => 'شراء واي فاي',
    'plural-model-label' => 'شراء واي فاي',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات الشراء',
                'fields' => [
                    'partner_id'       => 'العميل',
                    'wifi_package_id'  => 'حزمة واي فاي',
                    'cloud_id'         => 'السحابة',
                ],
                'helper' => [
                    'partner_id'      => 'اختر العميل الذي يقوم بشراء حزمة الواي فاي.',
                    'wifi_package_id' => 'اختر حزمة الواي فاي التي يتم شراؤها.',
                    'cloud_id'        => 'إذا كان لدى العميل سحابة واحدة مخصصة له، فسيتم اختيارها تلقائيًا.',
                ],
            ],
        ],
        'buttons' => [
            'new-purchase' => 'شراء واي فاي',
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'المعرف',
            'partner'            => 'العميل',
            'package'            => 'الحزمة',
            'service_product'    => 'منتج الخدمة',
            'invoice'            => 'الفاتورة',
            'cloud'              => 'السحابة',
            'quantity'           => 'الكمية',
            'generated_quantity' => 'تم توليده',
            'remaining_quantity' => 'المتبقي',
            'updated_at'         => 'آخر تحديث',
        ],
    ],

    'messages' => [
        'purchase_success'   => 'تم شراء واي فاي بنجاح.',
        'select_package'     => 'يرجى اختيار حزمة واي فاي.',
        'select_cloud'       => 'يرجى اختيار سحابة.',
        'select_customer'    => 'يرجى اختيار عميل.',
        'package_currency'   => 'الحزمة المختارة لا تحتوي على عملة. يرجى تحديث الحزمة أولاً',
        'cloud_assigned'     => 'السحابة المختارة غير مخصصة لهذا العميل.',
        'no_sales'           => 'لم يتم العثور على دفتر مبيعات. يرجى تكوين دفتر مبيعات واحد على الأقل أولاً.',
    ],

];
