<?php

return [
    'setup' => [
        'title' => 'مرفقات',
        'tooltip' => 'تحميل مرفقات',

        'form' => [
            'fields' => [
                'files' => 'الملفات',
                'attachment-helper-text' => 'أقصى حجم ملف: 10ميجا. الأنواع المسموحة: صور، PDF، Word، Excel، نص',

                'actions' => [
                    'delete' => [
                        'title' => 'تم حذف الملف',
                        'body' => 'تم حذف الملف بنجاح.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'تم تحميل المرفقات',
                    'body' => 'تم تحميل المرفقات بنجاح.',
                ],

                'warning' => [
                    'title' => 'لا توجد ملفات جديدة',
                    'body' => 'جميع الملفات تم تحميلها مسبقاً.',
                ],

                'error' => [
                    'title' => 'خطأ في تحميل المرفق',
                    'body' => 'فشل في تحميل المرفقات',
                ],
            ],
        ],
    ],
];
