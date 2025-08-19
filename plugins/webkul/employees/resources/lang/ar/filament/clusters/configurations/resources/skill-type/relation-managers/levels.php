<?php

return [
    'form' => [
        'name' => 'الإسم',
        'level' => 'المستوى',
        'default-level' => 'المستوى الافتراضي',
    ],

    'table' => [
        'columns' => [
            'name' => 'الإسم',
            'level' => 'المستوى',
            'default-level' => 'المستوى الافتراضي',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'deleted-records' => 'السجلات المحذوفة',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء مستوى المهارة',
                    'body' => 'تم إنشاء مستوى المهارة بنجاح.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث مستوى المهارة',
                    'body' => 'تم تحديث مستوى المهارة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة مستوى المهارة',
                    'body' => 'تم استعادة مستوى المهارة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف مستوى المهارة',
                    'body' => 'تم حذف مستوى المهارة بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف مستويات المهارة',
                    'body' => 'تم حذف مستويات المهارة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف مستويات المهارة بشكل قسري',
                    'body' => 'تم حذف مستويات المهارة بشكل قسري بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة مستويات المهارة بشكل قسري',
                    'body' => 'تم استعادة مستويات المهارة بشكل قسري بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'الإسم',
            'level' => 'المستوى',
            'default-level' => 'المستوى الافتراضي',
        ],
    ],
];
