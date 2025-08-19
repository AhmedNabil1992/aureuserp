<?php

return [
    'title' => 'الحركات',

    'table' => [
        'columns' => [
            'date' => 'التاريخ',
            'reference' => 'المرجع',
            'product' => 'المنتج',
            'package' => 'الطرد',
            'lot' => 'الدفعة / الأرقام التسلسلية',
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
