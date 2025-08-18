<?php

return [
    'setup' => [
        'title' => 'تسجيل ملاحظة',
        'submit-title' => 'سجل',

        'form' => [
            'fields' => [
                'hide-subject' => 'إخفاء الموضوع',
                'add-subject' => 'إضافة موضوع',
                'subject' => 'الموضوع',
                'write-message-here' => 'اكتب ملاحظتك هنا',
                'attachments-helper-text' => 'أقصى حجم ملف: 10ميجا. الأنواع المسموحة: صور، PDF، Word، Excel، نص',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'تم إضافة الملاحظة',
                    'body' => 'تمت إضافة ملاحظتك بنجاح.',
                ],

                'error' => [
                    'title' => 'خطأ في إضافة الملاحظة',
                    'body' => 'فشل في إضافة ملاحظتك',
                ],
            ],
        ],
    ],
];
