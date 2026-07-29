<?php

return [
    'title' => 'سجل الخزنة',
    'table' => [
        'columns' => [
            'trx_date'  => 'التاريخ',
            'trx_time'  => 'الوقت',
            'trx_type'  => 'نوع الحركة',
            'trx_name'  => 'اسم الحركة',
            'amount'    => 'المبلغ',
            'username'  => 'المستخدم',
            'shift'     => 'الشيفت',
            'reference' => 'المرجع',
        ],
        'filters' => [
            'from'      => 'من',
            'until'     => 'إلى',
            'trx_type'  => 'نوع الحركة',
            'trx_name'  => 'اسم الحركة',
            'username'  => 'المستخدم',
            'reference' => 'المرجع',
        ],
        'summaries' => [
            'total_amount' => 'إجمالي الحركات',
        ],
        'empty_state' => [
            'heading'     => 'لا يوجد سجل حركات',
            'description' => 'لم يتم العثور على أي حركات خزنة في النطاق المحدد.',
        ],
    ],
];
