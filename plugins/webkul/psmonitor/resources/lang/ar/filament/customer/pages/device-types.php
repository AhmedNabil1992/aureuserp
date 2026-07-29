<?php

return [
    'title' => 'أنواع الأجهزة',
    'table' => [
        'columns' => [
            'id'          => 'ID',
            'name'        => 'الاسم',
            'description' => 'الوصف',
            'is_active'   => 'مفعل',
        ],
        'filters' => [
            'active_only' => 'المفعلة فقط',
        ],
        'actions' => [
            'add_play_type' => 'إضافة نوع لعب',
        ],
        'empty_state' => [
            'heading' => 'لا توجد أنواع أجهزة للعرض',
        ],
    ],
];
