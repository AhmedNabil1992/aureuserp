<?php

return [
    'title' => 'سجل مبيعات الأصناف',
    'table' => [
        'columns' => [
            'trx_date'    => 'التاريخ',
            'trx_time'    => 'الوقت',
            'invoice_no'  => 'رقم الفاتورة',
            'client_name' => 'اسم الجهاز',
            'item_name'   => 'اسم الصنف',
            'quantity'    => 'الكمية',
            'price'       => 'السعر',
            'amount'      => 'الإجمالي',
            'username'    => 'المستخدم',
            'shift'       => 'الشيفت',
        ],
        'filters' => [
            'from'        => 'من',
            'until'       => 'إلى',
            'invoice_no'  => 'رقم الفاتورة',
            'client_name' => 'اسم الجهاز',
            'username'    => 'المستخدم',
            'item_id'     => 'رقم الصنف',
        ],
        'summaries' => [
            'total_amount' => 'إجمالي المبيعات',
        ],
        'empty_state' => [
            'heading'     => 'لا يوجد سجل أصناف',
            'description' => 'لم يتم العثور على أي مبيعات أصناف في النطاق المحدد.',
        ],
    ],
];
