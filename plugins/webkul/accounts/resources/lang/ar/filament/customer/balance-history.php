<?php

return [
    'title' => 'سجل الرصيد',
    'heading' => 'سجل طلبات الرصيد',

    'navigation' => [
        'label' => 'سجل الرصيد',
        'group' => 'الحساب',
    ],

    'table' => [
        'columns' => [
            'id' => 'الرقم',
            'amount' => 'المبلغ',
            'status' => 'الحالة',
            'request_date' => 'تاريخ الطلب',
            'approval_date' => 'تاريخ الموافقة',
            'notes' => 'الملاحظات',
        ],
        'filters' => [
            'status' => 'تصفية حسب الحالة',
        ],
        'actions' => [
            'view' => 'عرض التفاصيل',
        ],
    ],

    'statuses' => [
        'pending' => 'قيد الانتظار',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
        'processing' => 'قيد المعالجة',
        'completed' => 'مكتمل',
    ],

    'form' => [
        'section' => [
            'details' => 'التفاصيل',
        ],
        'fields' => [
            'amount' => 'المبلغ',
            'status' => 'الحالة',
            'request_date' => 'تاريخ الطلب',
            'approval_date' => 'تاريخ الموافقة',
            'notes' => 'الملاحظات',
        ],
    ],

    'notifications' => [
        'pending' => 'طلب الرصيد الخاص بك قيد الانتظار',
        'approved' => 'تمت الموافقة على طلب الرصيد الخاص بك',
        'rejected' => 'تم رفض طلب الرصيد الخاص بك',
    ],
];
