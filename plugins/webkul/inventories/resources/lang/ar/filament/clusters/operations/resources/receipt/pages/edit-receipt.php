<?php

return [
    'notification' => [
        'title' => 'تم تحديث الإيصال',
        'body' => 'تم تحديث الإيصال بنجاح.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'طباعة',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الإيصال',
                    'body' => 'تم حذف الإيصال بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف الإيصال',
                    'body' => 'لا يمكن حذف الإيصال لأنه قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
