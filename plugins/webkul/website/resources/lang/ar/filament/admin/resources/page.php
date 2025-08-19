<?php

return [
    'navigation' => [
        'title' => 'الصفحات',
        'group' => 'الموقع الإلكتروني',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'title' => 'العنوان',
                    'title-placeholder' => 'عنوان الصفحة ...',
                    'slug' => 'الرابط المختصر',
                    'content' => 'المحتوى',
                ],
            ],

            'seo' => [
                'title' => 'تحسين محركات البحث',

                'fields' => [
                    'meta-title' => 'عنوان الميتا',
                    'meta-keywords' => 'كلمات الميتا المفتاحية',
                    'meta-description' => 'وصف الميتا',
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'is-header-visible' => 'ظاهر في قائمة الهيدر',
                    'is-footer-visible' => 'ظاهر في قائمة الفوتر',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title' => 'العنوان',
            'slug' => 'الرابط المختصر',
            'creator' => 'أنشئ بواسطة',
            'is-published' => 'منشور',
            'is-header-visible' => 'ظاهر في قائمة الهيدر',
            'is-footer-visible' => 'ظاهر في قائمة الفوتر',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'is-published' => 'منشور',
            'creator' => 'أنشئ بواسطة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الصفحة',
                    'body' => 'تم تحديث الصفحة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة الصفحة',
                    'body' => 'تمت استعادة الصفحة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الصفحة',
                    'body' => 'تم حذف الصفحة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم الحذف الإجباري للصفحة',
                    'body' => 'تم الحذف الإجباري للصفحة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة الصفحات',
                    'body' => 'تمت استعادة الصفحات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الصفحات',
                    'body' => 'تم حذف الصفحات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم الحذف الإجباري للصفحات',
                    'body' => 'تم الحذف الإجباري للصفحات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'title' => 'العنوان',
                    'slug' => 'الرابط المختصر',
                    'content' => 'المحتوى',
                    'banner' => 'الصورة البارزة',
                ],
            ],

            'seo' => [
                'title' => 'تحسين محركات البحث',

                'entries' => [
                    'meta-title' => 'عنوان الميتا',
                    'meta-keywords' => 'كلمات الميتا المفتاحية',
                    'meta-description' => 'وصف الميتا',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'author' => 'المؤلف',
                    'created-by' => 'أنشئ بواسطة',
                    'published-at' => 'تاريخ النشر',
                    'last-updated-by' => 'آخر تحديث بواسطة',
                    'last-updated' => 'آخر تحديث في',
                    'created-at' => 'تاريخ الإنشاء',
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'entries' => [
                    'is-header-visible' => 'ظاهر في قائمة الهيدر',
                    'is-footer-visible' => 'ظاهر في قائمة الفوتر',
                ],
            ],
        ],
    ],
];
