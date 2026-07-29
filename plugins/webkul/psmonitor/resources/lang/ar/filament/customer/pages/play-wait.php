<?php

return [
    'title' => 'جلسات اللعب الحالية',
    'table' => [
        'columns' => [
            'order_no'    => 'رقم الطلب',
            'device_name' => 'اسم الجهاز',
            'start_time'  => 'وقت البداية',
            'end_time'    => 'وقت النهاية',
            'period'      => 'المدة',
            'cost'        => 'التكلفة',
            'play_type'   => 'نوع اللعب',
            'play_price'  => 'سعر اللعب',
            'user_name'   => 'المستخدم',
            'shift'       => 'الشيفت',
        ],
        'actions' => [
            'device_current' => 'السجل الحالي',
        ],
        'summaries' => [
            'total_cost' => 'إجمالي تكلفة اللعب الحالي',
        ],
        'empty_state' => [
            'heading'     => 'لا يوجد بيانات لعب حالية',
            'description' => 'لا تتوفر أي جلسات لعب نشطة حالياً.',
        ],
    ],
];
