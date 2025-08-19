<?php

return [
    'title' => 'جداول العمل',

    'navigation' => [
        'title' => 'جداول العمل',
        'group' => 'الموظف',
    ],

    'groups' => [
        'status' => 'الحالة',
        'created-by' => 'أنشئ بواسطة',
        'created-at' => 'تاريخ الإنشاء',
        'updated-at' => 'تاريخ التحديث',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',
                'fields' => [
                    'name' => 'الاسم',
                    'schedule-name' => 'اسم الجدول',
                    'schedule-name-tooltip' => 'يرجى كتابة اسم جدول عمل وصفي.',
                    'timezone' => 'المنطقة الزمنية',
                    'timezone-tooltip' => 'يرجى اختيار المنطقة الزمنية لجدول العمل.',
                    'company' => 'الشركة',
                ],
            ],

            'configuration' => [
                'title' => 'إعدادات ساعات العمل',
                'fields' => [
                    'hours-per-day' => 'ساعات في اليوم',
                    'hours-per-day-suffix' => 'ساعات',
                    'full-time-required-hours' => 'ساعات العمل المطلوبة للدوام الكامل',
                    'full-time-required-hours-suffix' => 'ساعات في الأسبوع',
                ],
            ],

            'flexibility' => [
                'title' => 'المرونة',
                'fields' => [
                    'status' => 'الحالة',
                    'two-weeks-calendar' => 'جدول أسبوعين',
                    'two-weeks-calendar-tooltip' => 'تفعيل جدول عمل متناوب كل أسبوعين.',
                    'flexible-hours' => 'ساعات مرنة',
                    'flexible-hours-tooltip' => 'السماح للموظفين بساعات عمل مرنة.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'المعرف',
            'name' => 'اسم الجدول',
            'timezone' => 'المنطقة الزمنية',
            'company' => 'الشركة',
            'flexible-hours' => 'ساعات مرنة',
            'status' => 'الحالة',
            'daily-hours' => 'ساعات يومية',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'company' => 'الشركة',
            'is-active' => 'الحالة',
            'two-week-calendar' => 'جدول أسبوعين',
            'flexible-hours' => 'ساعات مرنة',
            'timezone' => 'المنطقة الزمنية',
            'name' => 'اسم الجدول',
            'attendance' => 'الحضور',
            'created-by' => 'أنشئ بواسطة',
            'daily-hours' => 'ساعات يومية',
            'updated-at' => 'تاريخ التحديث',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'groups' => [
            'name' => 'اسم الجدول',
            'status' => 'الحالة',
            'timezone' => 'المنطقة الزمنية',
            'flexible-hours' => 'ساعات مرنة',
            'daily-hours' => 'ساعات يومية',
            'created-by' => 'أنشئ بواسطة',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة خطة الجدول',
                    'body' => 'تم استعادة خطة الجدول بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف خطة الجدول',
                    'body' => 'تم حذف خطة الجدول بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف خطة الجدول نهائياً',
                    'body' => 'تم حذف خطة الجدول نهائياً بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة خطط الجداول',
                    'body' => 'تم استعادة خطط الجداول بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف خطط الجداول',
                    'body' => 'تم حذف خطط الجداول بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'تم حذف خطط الجداول نهائياً',
                    'body' => 'تم حذف خطط الجداول نهائياً بنجاح.',
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
                    'schedule-name' => 'اسم الجدول',
                    'schedule-name-tooltip' => 'يرجى كتابة اسم جدول عمل وصفي.',
                    'timezone' => 'المنطقة الزمنية',
                    'timezone-tooltip' => 'يرجى اختيار المنطقة الزمنية لجدول العمل.',
                    'company' => 'الشركة',
                ],
            ],

            'configuration' => [
                'title' => 'إعدادات ساعات العمل',
                'entries' => [
                    'hours-per-day' => 'ساعات في اليوم',
                    'hours-per-day-suffix' => 'ساعات',
                    'full-time-required-hours' => 'ساعات العمل المطلوبة للدوام الكامل',
                    'full-time-required-hours-suffix' => 'ساعات في الأسبوع',
                ],
            ],

            'flexibility' => [
                'title' => 'المرونة',
                'entries' => [
                    'status' => 'الحالة',
                    'two-weeks-calendar' => 'جدول أسبوعين',
                    'two-weeks-calendar-tooltip' => 'تفعيل جدول عمل متناوب كل أسبوعين.',
                    'flexible-hours' => 'ساعات مرنة',
                    'flexible-hours-tooltip' => 'السماح للموظفين بساعات عمل مرنة.',
                ],
            ],
        ],
    ],
];
