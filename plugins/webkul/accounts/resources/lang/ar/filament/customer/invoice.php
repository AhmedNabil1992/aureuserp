<?php

return [
    'navigation' => [
        'label' => 'فواتيري',
        'group' => 'الحسابات',
    ],

    'models' => [
        'singular' => 'فاتورة',
        'plural'   => 'فواتيري',
    ],

    'pages' => [
        'view' => [
            'sections' => [
                'details' => 'تفاصيل الفاتورة',
            ],
            'tabs' => [
                'invoice_lines' => 'منتجات الفاتورة',
            ],
            'columns' => [
                'invoice-number' => 'رقم الفاتورة',
                'product'    => 'المنتج',
                'quantity'   => 'الكمية',
                'unit_price' => 'سعر الوحدة',
                'subtotal'   => 'الإجمالي الفرعي',
            ],
            'entries' => [
                'invoice_lines' => 'بنود الفاتورة',
            ],
        ],
    ],
];
