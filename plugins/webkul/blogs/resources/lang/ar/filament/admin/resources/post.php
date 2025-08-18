<?php

return [
    'navigation' => [
        'title' => 'مشاركات المدونة',
        'group' => 'الموقع',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'title' => 'العنوان',
                    'sub-title' => 'العنوان الفرعي',
                    'title-placeholder' => 'عنوان المقال...',
                    'slug' => 'المعرف (Slug)',
                    'content' => 'المحتوى',
                    'banner' => 'الغلاف',
                ],
            ],

            'seo' => [
                'title' => 'تحسين محركات البحث (SEO)',

                'fields' => [
                    'meta-title' => 'عنوان الميتا',
                    'meta-keywords' => 'كلمات الميتا',
                    'meta-description' => 'وصف الميتا',
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'category' => 'التصنيف',
                    'tags' => 'الوسوم',
                    'name' => 'الاسم',
                    'color' => 'اللون',
                    'is-published' => 'منشور',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title' => 'العنوان',
            'slug' => 'المعرف',
            'author' => 'المؤلف',
            'category' => 'التصنيف',
            'creator' => 'أنشأ بواسطة',
            'is-published' => 'منشور',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'آخر تعديل',
        ],

        'groups' => [
            'category' => 'التصنيف',
            'author' => 'المؤلف',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'is-published' => 'منشور',
            'author' => 'المؤلف',
            'creator' => 'أنشأ بواسطة',
            'category' => 'التصنيف',
            'tags' => 'الوسوم',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث المقال',
                    'body' => 'تم تحديث المقال بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المقال',
                    'body' => 'تم استعادة المقال بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المقال',
                    'body' => 'تم حذف المقال بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم الحذف النهائي للمقال',
                    'body' => 'تم حذف المقال نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المقالات',
                    'body' => 'تم استعادة المقالات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المقالات',
                    'body' => 'تم حذف المقالات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم الحذف النهائي للمقالات',
                    'body' => 'تم حذف المقالات نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'entries' => [
                    'title' => 'العنوان',
                    'slug' => 'المعرف',
                    'content' => 'المحتوى',
                    'banner' => 'الغلاف',
                ],
            ],

            'seo' => [
                'title' => 'تحسين محركات البحث (SEO)',

                'entries' => [
                    'meta-title' => 'عنوان الميتا',
                    'meta-keywords' => 'كلمات الميتا',
                    'meta-description' => 'وصف الميتا',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'author' => 'المؤلف',
                    'created-by' => 'أنشأ بواسطة',
                    'published-at' => 'تاريخ النشر',
                    'last-updated-by' => 'آخر من قام بالتعديل',
                    'last-updated' => 'آخر تعديل',
                    'created-at' => 'تاريخ الإنشاء',
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'entries' => [
                    'category' => 'التصنيف',
                    'tags' => 'الوسوم',
                    'name' => 'الاسم',
                    'color' => 'اللون',
                    'is-published' => 'منشور',
                ],
            ],
        ],
    ],
];
