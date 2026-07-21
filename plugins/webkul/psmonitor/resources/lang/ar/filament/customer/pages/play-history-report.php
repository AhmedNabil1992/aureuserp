<?php

return [
    'title' => 'سجل الجلسات واللعب',
    'navigation_group' => 'مراقبة الفروع',
    'table' => [
        'columns' => [
            'trx_date' => 'التاريخ',
            'invoice_no' => 'رقم الفاتورة',
            'device_name' => 'اسم الجهاز / الغرفة',
            'play_type' => 'نوع اللعب',
            'hour_price' => 'سعر الساعة',
            'play_time' => 'المدة (دقيقة)',
            'cost' => 'تكلفة الجلسة',
            'start_time' => 'وقت البداية',
            'end_time' => 'وقت النهاية',
            'username' => 'المستخدم',
            'shift_no' => 'رقم الشيفت',
        ],
        'summaries' => [
            'total_minutes' => 'إجمالي الدقائق',
            'total_cost' => 'إجمالي الإيرادات',
            'count' => 'عدد الجلسات',
        ],
        'filters' => [
            'from' => 'من تاريخ',
            'until' => 'إلى تاريخ',
        ],
    ],
    'notifications' => [
        'connection_failed' => [
            'title' => 'تعذر الاتصال بقاعدة بيانات الفرع',
            'body' => 'السيرفر الخاص بالفرع المختار غير متاح حالياً.',
        ],
    ],
];
