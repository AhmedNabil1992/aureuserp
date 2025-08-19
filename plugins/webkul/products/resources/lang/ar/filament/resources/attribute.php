<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name' => 'الاسم',
                    'type' => 'النوع',
                ],
            ],

            'options' => [
                'title' => 'الخيارات',

                'fields' => [
                    'name' => 'الاسم',
                    'color' => 'اللون',
                    'extra-price' => 'سعر إضافي',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'type' => 'النوع',
            'deleted-at' => 'تاريخ الحذف',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'type' => 'النوع',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'type' => 'النوع',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة السمة',
                    'body' => 'تم استعادة السمة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف السمة',
                    'body' => 'تم حذف السمة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم الحذف الإجباري للسمة',
                        'body' => 'تم الحذف الإجباري للسمة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف السمة',
                        'body' => 'لا يمكن حذف السمة لأنها قيد الاستخدام حاليًا.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة السمات',
                    'body' => 'تم استعادة السمات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف السمات',
                    'body' => 'تم حذف السمات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم الحذف الإجباري للسمات',
                        'body' => 'تم الحذف الإجباري للسمات بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف السمات',
                        'body' => 'لا يمكن حذف السمات لأنها قيد الاستخدام حاليًا.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'name' => 'الاسم',
                    'type' => 'النوع',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'creator' => 'أنشئ بواسطة',
                    'created_at' => 'تاريخ الإنشاء',
                    'updated_at' => 'آخر تحديث في',
                ],
            ],
        ],
    ],
];
