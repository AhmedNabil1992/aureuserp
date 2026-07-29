<?php

return [
    'title' => 'سجل المصروفات',
    'table' => [
        'columns' => [
            'id'              => '#',
            'expenses_type'   => 'نوع المصروف',
            'expenses_remark' => 'البيان',
            'expenses_amt'    => 'المبلغ',
            'username'        => 'المستخدم',
            'shift'           => 'الشيفت',
            'trx_date'        => 'التاريخ',
            'trx_time'        => 'الوقت',
        ],
        'summaries' => [
            'total' => 'إجمالي المصروفات',
        ],
        'filters' => [
            'from'  => 'من',
            'until' => 'إلى',
        ],
        'actions' => [
            'add_expense'    => 'إضافة مصروف',
            'delete_expense' => 'حذف',
        ],
        'empty_state' => [
            'heading' => 'لا يوجد مصروفات للعرض',
        ],
    ],
];
