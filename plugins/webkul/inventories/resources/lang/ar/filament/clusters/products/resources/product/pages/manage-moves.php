<?php

return [
    'title' => 'الوارد/الصادر',

    'tabs' => [
        'todo' => 'قيد التنفيذ',
        'done' => 'منجز',
        'incoming' => 'الوارد',
        'outgoing' => 'الصادر',
        'internal' => 'داخلي',
    ],

    'table' => [
        'columns' => [
            'date' => 'التاريخ',
            'reference' => 'المرجع',
            'product' => 'المنتج',
            'package' => 'الطرد',
            'lot' => 'دفعة / أرقام تسلسلية',
            'source-location' => 'الموقع المصدر',
            'destination-location' => 'الموقع الوجهة',
            'quantity' => 'الكمية',
            'state' => 'الحالة',
            'done-by' => 'تم بواسطة',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحركة',
                    'body' => 'تم حذف الحركة بنجاح.',
                ],
            ],
        ],
    ],
];
