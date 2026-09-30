<?php

return [
    'navigation' => [
        'label'    => 'تحديثات أجهزة IoT',
        'singular' => 'تحديث Firmware',
    ],
    'form' => [
        'fields' => [
            'device_model'        => 'موديل الجهاز',
            'device_model_helper' => 'يجب أن يطابق تمامًا قيمة الهيدر x-ESP8266-model.',
            'version'             => 'رقم الإصدار',
            'version_helper'      => 'استخدم إصدارًا قابلًا للمقارنة مثل 1.0.2.',
            'file'                => 'ملف Firmware',
            'file_helper'         => 'ارفع ملف .bin المترجم، وسيتم حفظه بصورة خاصة.',
            'is_active'           => 'متاح للأجهزة',
            'notes'               => 'ملاحظات',
        ],
    ],
    'table' => [
        'columns' => [
            'device_model' => 'موديل الجهاز',
            'version'      => 'الإصدار',
            'file'         => 'الملف',
            'file_size'    => 'الحجم',
            'is_active'    => 'مفعّل',
            'updated_at'   => 'آخر تحديث',
        ],
    ],
];
