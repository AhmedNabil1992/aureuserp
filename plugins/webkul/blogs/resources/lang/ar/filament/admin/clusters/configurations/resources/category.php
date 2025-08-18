<?php

return [
    'navigation' => [
        'title' => 'الفئات',
        'group' => 'مدونة',
    ],

    'form' => [
        'fields' => [
            'name' => 'اسم الفئة',
            'name-placeholder' => 'عنوان الفئة ...',
            'sub-title' => 'العنوان الفرعي',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'اسم الفئة',
            'sub-title' => 'العنوان الفرعي',
            'posts' => 'المنشورات',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'is-published' => 'هل تم النشر',
            'author' => 'المؤلف',
            'creator' => 'تم إنشاؤه بواسطة',
            'category' => 'الفئة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الفئة',
                    'body' => 'تم تحديث الفئة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الفئة',
                    'body' => 'تم استعادة الفئة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفئة',
                    'body' => 'تم حذف الفئة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الفئة بشكل قسري',
                    'body' => 'تم حذف الفئة بشكل قسري بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الفئات',
                    'body' => 'تم استعادة الفئات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفئات',
                    'body' => 'تم حذف الفئات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الفئات بشكل قسري',
                    'body' => 'تم حذف الفئات بشكل قسري بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
    ],
];
