<?php

return [
    'title'            => 'ملخص استخدام الإنترنت',
    'navigation_label' => 'ملخص الاستخدام',
    'empty'            => [
        'heading'     => 'لا توجد بيانات استخدام حالياً',
        'description' => 'لم يتم العثور على أية جلسات استخدام للإنترنت خلال الفترة المحددة.',
    ],
    'tabs' => [
        'all'     => 'الكل',
        'voucher' => 'الكروت (Vouchers)',
        'user'    => 'المستخدمين الدائمين (Users)',
    ],
    'columns' => [
        'username'              => 'رقم الكارت / المستخدم',
        'type'                  => 'النوع',
        'sessions_count'        => 'عدد الجلسات',
        'total_session_seconds' => 'إجمالي الوقت',
        'total_input_octets'    => 'التحميل (الوارد)',
        'total_output_octets'   => 'الرفع (الصادر)',
        'total_octets'          => 'إجمالي البيانات',
        'last_session_at'       => 'آخر جلسة',
    ],
    'filters' => [
        'period'     => 'تصفية حسب الفترة',
        'start_date' => 'من تاريخ',
        'end_date'   => 'إلى تاريخ',
    ],
    'units' => [
        'hours'   => 'ساعة',
        'minutes' => 'دقيقة',
    ],
];