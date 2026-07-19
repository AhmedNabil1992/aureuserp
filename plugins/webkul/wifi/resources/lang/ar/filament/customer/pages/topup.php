<?php

return [
    'title' => 'الإضافات',
    'table' => [
        'columns' => [
            'cloud' => 'السحابه',
            'permanent_user' => 'المستخدم الدائم',
            'data' => 'البيانات',
            'time' => 'الوقت ',
            'days_to_use' => 'أيام الاستخدام',
            'comment' => 'تعليق',
            'created' => 'تم الإنشاء',
            'modified' => 'تم التعديل',
        ],
    ],
    'actions' => [
        'title' => 'إضافة Topup',
        'modal_heading' => 'إضافة بيانات جديدة',
    ],
    'headeractions' => [
        'form' => [
            'cloud' => 'السحابه',
            'username' => 'اسم المستخدم',
            'type' => 'النوع',
            'value' => 'القيمة',
            'data_unit' => 'الوحدة',
            'comment' => 'تعليق',
        ],
    ],
    'notifications' => [
        'topup_success' => 'تمت إضافة Topup بنجاح',
        'topup_failed' => 'فشل إضافة Topup',
    ],
];