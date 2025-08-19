<?php

return [
    'modal-actions' => [
        'edit' => [
            'title' => 'تعديل',
        ],

        'delete' => [
            'title' => 'حذف',
        ],
    ],

    'view-action' => [
        'title' => 'عرض',
        'description' => 'عرض طلب الإجازة',
    ],

    'header-actions' => [
        'create' => [
            'title' => 'إجازة جديدة',
            'description' => 'إنشاء طلب إجازة',

            'employee-not-found' => [
                'notification' => [
                    'title' => 'الموظف غير موجود',
                    'body' => 'يرجى إضافة موظف إلى ملفك الشخصي قبل إنشاء طلب إجازة.',
                ],
            ],
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type' => 'نوع الإجازة',
            'request-date-from' => 'تاريخ بدء الطلب',
            'request-date-to' => 'تاريخ نهاية الطلب',
            'period' => 'الفترة',
            'half-day' => 'نصف يوم',
            'requested-days' => 'الأيام/الساعات المطلوبة',
            'description' => 'الوصف',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type' => 'Time Off Type',
            'request-date-from' => 'Request Date From',
            'request-date-to' => 'Request Date To',
            'description' => 'Description',
            'description-placeholder' => 'No description provided',
            'duration' => 'Duration',
            'status' => 'Status',
        ],
    ],

    'events' => [
        'title' => ':name On :status: :days day(s)',
    ],
];
