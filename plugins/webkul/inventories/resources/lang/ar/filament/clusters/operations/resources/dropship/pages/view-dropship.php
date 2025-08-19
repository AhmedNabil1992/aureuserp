<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'طباعة',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الدروب شيب',
                    'body' => 'تم حذف الدروب شيب بنجاح.',
                ],

                'error' => [
                    'title' => 'تعذر حذف الدروب شيب',
                    'body' => 'لا يمكن حذف الدروب شيب لأنه قيد الاستخدام حالياً.',
                ],
            ],
        ],
    ],
];
