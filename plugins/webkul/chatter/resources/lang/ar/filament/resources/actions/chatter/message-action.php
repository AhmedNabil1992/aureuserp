<?php

return [
    'setup' => [
        'title' => 'إرسال رسالة',
        'submit-title' => 'إرسال',

        'form' => [
            'fields' => [
                'hide-subject' => 'إخفاء الموضوع',
                'add-subject' => 'إضافة موضوع',
                'subject' => 'الموضوع',
                'write-message-here' => 'اكتب رسالتك هنا',
                'attachments-helper-text' => 'أقصى حجم ملف: 10ميجا. الأنواع المسموحة: صور، PDF، Word، Excel، نص',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'تم إرسال الرسالة',
                    'body' => 'تم إرسال رسالتك بنجاح.',
                ],

                'error' => [
                    'title' => 'خطأ في إرسال الرسالة',
                    'body' => 'فشل في إرسال رسالتك',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
