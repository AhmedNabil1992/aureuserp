<?php

return [
    'setup' => [
        'title' => 'المتابعون',
        'submit-action-title' => 'إضافة متابع',
        'tooltip' => 'إضافة متابع',

        'form' => [
            'fields' => [
                'recipients' => 'المستلمون',
                'notify-user' => 'إعلام المستخدم',
                'add-a-note' => 'إضافة ملاحظة',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'تم إضافة المتابع',
                    'body' => '\":partner\" تم إضافته كمتابع.',
                ],

                'error' => [
                    'title' => 'خطأ في إضافة المتابع',
                    'body' => 'فشل في إضافة \":partner\" كمتابع',
                ],
            ],

            'mail' => [
                'subject' => 'دعوة للمتابعة :model: :department',
            ],
        ],
    ],
];
