<?php

return [
    'title' => 'إعدادات الأجهزة',
    'table' => [
        'columns' => [
            'device_name' => 'اسم الجهاز',
            'device_type' => 'نوع الجهاز',
            'kind'        => 'نمط التشغيل',
            'ip_address'  => 'IP Address',
            'status'      => 'الحالة',
            'limit_time'  => 'الحد الأدنى',
            'is_active'   => 'مفعل',
        ],
        'filters' => [
            'is_active' => 'المفعلة فقط',
        ],
        'actions' => [
            'edit' => 'تعديل',
        ],
        'empty_state' => [
            'heading' => 'لا توجد أجهزة للعرض',
        ],
    ],
];
