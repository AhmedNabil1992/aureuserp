<?php

return [
    'title' => 'طلبات الكافيه الحالية',
    'table' => [
        'columns' => [
            'order_no'    => 'رقم الطلب',
            'device_name' => 'اسم الجهاز',
            'item_name'   => 'اسم الصنف',
            'quantity'    => 'الكمية',
            'price'       => 'السعر',
            'amount'      => 'الإجمالي',
            'print'       => 'تم الطباعة',
            'order_by'    => 'بواسطة',
            'notes'       => 'ملاحظات',
        ],
        'actions' => [
            'device_current' => 'السجل الحالي',
        ],
        'summaries' => [
            'total_amount' => 'إجمالي الطلبات',
        ],
        'empty_state' => [
            'heading'     => 'لا يوجد طلبات حالية',
            'description' => 'لا تتوفر أي طلبات كافيه حالية في انتظار التسوية.',
        ],
    ],
];
