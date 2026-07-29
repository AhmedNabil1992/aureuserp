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

    'table' => [
        'columns' => [
            'invoice_number' => 'رقم الفاتورة',
            'invoice_date'   => 'تاريخ الفاتورة',
            'due_date'       => 'تاريخ الاستحقاق',
            'total'          => 'الإجمالي النهائي',
            'amount_due'     => 'المبلغ المتبقي',
            'status'         => 'حالة الفاتورة',
            'payment_status' => 'حالة الدفع',
            'customer'       => 'العميل',
        ],
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
                'invoice_number' => 'رقم الفاتورة',
                'product'        => 'المنتج',
                'quantity'       => 'الكمية',
                'unit_price'     => 'سعر الوحدة',
                'subtotal'       => 'الإجمالي الفرعي',
            ],
            'entries' => [
                'invoice_lines' => 'بنود الفاتورة',
            ],
        ],
    ],
];
