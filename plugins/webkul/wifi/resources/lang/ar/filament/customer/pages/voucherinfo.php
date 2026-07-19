<?php

return [
    'navigation' => [
        'title' => 'بيانات الكروت',
    ],
    'table' => [
        'columns' => [
            'cloud' => 'السحابة',
            'realm' => 'المجموعة',
            'batch' => 'الدفعة',
            'name' => 'رقم الكارت',
            'profile' => 'الباقة',
            'status' => 'الحالة',
            'status_type' => [
                'new'      => 'جديد',
                'used'     => 'مستخدم',
                'depleted' => 'مستنفذ',
                'expired'  => 'منتهي',
            ],
            'perc_time_used' => 'نسبة الوقت المستخدم',
            'perc_data_used' => 'نسبة البيانات المستخدمة',
            'last_accept_time' => 'آخر وقت قبول',
            'last_reject_time' => 'آخر وقت رفض',
            'last_accept_nas' => 'آخر جهاز قبول',
            'last_reject_nas' => 'آخر جهاز رفض',
            'last_reject_message' => 'آخر رسالة رفض',
            'expires' => 'ينتهي في',
            'time_valid' => 'وقت الصلاحية',
            'created' => 'تم الإنشاء',
            'modified' => 'تم التعديل',
        ],
        'actions' => [
            'view' => 'تفاصيل الإستهلاك',
        ],
    ],
    'view' => [
        'title' => 'سجل استهلاك الكارت',
        'cancel' => 'إغلاق',
        'no_record' => 'لا توجد سجلات استهلاك أو اتصالات نشطة لهذا الكارت حتى الآن.',
        'table' => [
            'mac' => 'عنوان الماك',
            'start_time' => 'وقت البداية',
            'stop_time' => 'وقت النهاية',
            'session_time' => 'مدة الجلسة',
            'data_in' => 'البيانات الواردة',
            'data_out' => 'البيانات الصادرة',
        ]
    ],
];