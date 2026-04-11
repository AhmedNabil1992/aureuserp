<?php

return [
    'navigation' => [
        'group' => 'الإعدادات',
        'title' => 'المدن',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'عام',
                'fields' => [
                    'country'            => 'الدولة',
                    'governorate'        => 'المحافظة',
                    'governorate-helper' => 'اختر الدولة أولا',
                    'name'               => 'اسم المدينة',
                    'name-ar'            => 'اسم المدينة بالعربي',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'country'        => 'الدولة',
            'name'           => 'المدينة',
            'name-ar'        => 'المدينة (عربي)',
            'governorate'    => 'المحافظة',
            'governorate-ar' => 'المحافظة (عربي)',
            'created-at'     => 'تاريخ الإنشاء',
        ],
        'filters' => [
            'location'    => 'الموقع',
            'country'     => 'الدولة',
            'governorate' => 'المحافظة',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المدينة',
                    'body'  => 'تم حذف المدينة بنجاح.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المدن',
                    'body'  => 'تم حذف المدن المحددة بنجاح.',
                ],
            ],
        ],
    ],

    'pages' => [
        'list-city' => [
            'header-actions' => [
                'create' => [
                    'label' => 'إضافة مدينة',
                ],
            ],
        ],
        'create-city' => [
            'notification' => [
                'title' => 'تم إنشاء المدينة',
                'body'  => 'تم إنشاء المدينة بنجاح.',
            ],
        ],
        'edit-city' => [
            'notification' => [
                'title' => 'تم تحديث المدينة',
                'body'  => 'تم تحديث المدينة بنجاح.',
            ],
            'header-actions' => [
                'delete' => [
                    'notification' => [
                        'title' => 'تم حذف المدينة',
                        'body'  => 'تم حذف المدينة بنجاح.',
                    ],
                ],
            ],
        ],
    ],
];
