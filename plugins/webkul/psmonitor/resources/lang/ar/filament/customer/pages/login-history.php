<?php

return [
    'title' => 'سجل الدخول',
    'table' => [
        'columns' => [
            'id'          => 'ID',
            'user_id'     => 'رقم المستخدم',
            'user_name'   => 'اسم المستخدم',
            'date'        => 'التاريخ',
            'time'        => 'الوقت',
            'ip_address'  => 'عنوان IP',
            'remark'      => 'ملاحظة',
        ],
        'filters' => [
            'from'  => 'من',
            'until' => 'إلى',
        ],
        'empty_state' => [
            'heading'     => 'لا يوجد تسجيلات دخول بعد',
            'description' => 'لم يتم العثور على أي بيانات تسجيل دخول في النطاق المحدد.',
        ],
    ],
];
