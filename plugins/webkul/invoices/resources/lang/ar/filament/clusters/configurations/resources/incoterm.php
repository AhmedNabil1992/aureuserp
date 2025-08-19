<?php

return [
    'title' => 'شروط الإنكوترمز',

    'navigation' => [
        'title' => 'شروط الإنكوترمز',
        'group' => 'الفواتير',
    ],

    'global-search' => [
        'name' => 'الاسم',
        'code' => 'الرمز',
    ],

    'form' => [
        'fields' => [
            'code' => 'الرمز',
            'name' => 'الاسم',
        ],
    ],

    'table' => [
        'columns' => [
            'code' => 'الرمز',
            'name' => 'الاسم',
            'created-by' => 'أنشئ بواسطة',
        ],

        'groups' => [
            'code' => 'الرمز',
            'name' => 'الاسم',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الإنكوترم',
                    'body' => 'تم تحديث الإنكوترم بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الإنكوترم',
                    'body' => 'تم حذف الإنكوترم بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الإنكوترم',
                    'body' => 'تم استعادة الإنكوترم بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة الإنكوترمز',
                    'body' => 'تم استعادة الإنكوترمز بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الإنكوترمز',
                    'body' => 'تم حذف الإنكوترمز بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف الإنكوترمز نهائياً',
                    'body' => 'تم حذف الإنكوترمز نهائياً بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'الاسم',
            'code' => 'الرمز',
        ],
    ],
];
