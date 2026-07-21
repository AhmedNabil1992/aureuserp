<?php

return [
    'title' => 'تقرير تسليم الوردية والنقدية',
    'navigation_group' => 'مراقبة الفروع',
    'table' => [
        'columns' => [
            'shift_no' => 'رقم الشيفت',
            'shift_date' => 'التاريخ',
            'shift_open' => 'فتح بواسطة',
            'shift_close' => 'إغلاق بواسطة',
            'start_amt' => 'درج البداية',
            'playstation' => 'إيراد البلايستيشن',
            'sales_amt' => 'إيراد البوفيه',
            'expenses_amt' => 'المصروفات',
            'remain_amt' => 'المستحق في الدرج',
            'actual_amt' => 'الفعلي المسلم',
            'different' => 'عجز / زيادة',
            'status' => 'الحالة',
        ],
        'summaries' => [
            'total_playstation' => 'إجمالي إيراد اللعب',
            'total_sales' => 'إجمالي مبيعات البوفيه',
            'total_expenses' => 'إجمالي المصروفات',
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
