<?php

return [
    'title' => 'أسعار اللعب',
    'table' => [
        'columns' => [
            'device_type' => 'نوع الجهاز',
            'device_name' => 'اسم الجهاز',
            'game_type'   => 'نوع اللعب',
            'hour_price'  => 'سعر الساعة',
            's_from'      => 'يبدأ من',
        ],
        'actions' => [
            'add_new_price' => 'إضافة سعر جديد',
            'edit_price'    => 'تعديل السعر',
        ],
        'empty_state' => [
            'heading' => 'لا توجد أسعار للعرض',
        ],
    ],
];
