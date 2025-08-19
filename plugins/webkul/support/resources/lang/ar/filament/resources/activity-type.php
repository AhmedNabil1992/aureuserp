<?php

return [
    'title' => 'الأقسام',

    'navigation' => [
        'title' => 'الأقسام',
        'group' => 'الموظفون',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'معلومات عامة',

                'fields' => [
                    'name' => 'نوع النشاط',
                    'name-tooltip' => 'أدخل اسم نوع النشاط الرسمي',
                    'action' => 'إجراء',
                    'default-user' => 'المستخدم الافتراضي',
                    'summary' => 'ملخص',
                    'note' => 'ملاحظة',
                ],
            ],

            'delay-information' => [
                'title' => 'معلومات التأخير',

                'fields' => [
                    'delay-count' => 'عدد التأخيرات',
                    'delay-unit' => 'وحدة التأخير',
                    'delay-form' => 'شكل التأخير',
                    'delay-form-helper-text' => 'مصدر حساب التأخير',
                ],
            ],

            'advanced-information' => [
                'title' => 'معلومات متقدمة',

                'fields' => [
                    'icon' => 'أيقونة',
                    'decoration-type' => 'نوع الزخرفة',
                    'chaining-type' => 'نوع التسلسل',
                    'suggest' => 'اقتراح',
                    'trigger' => 'تشغيل',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'الحالة والإعدادات',

                'fields' => [
                    'status' => 'Status',
                    'keep-done-activities' => 'Keep Done Activities',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Activity Type',
            'summary' => 'Summary',
            'planned-in' => 'Planned In',
            'type' => 'Type',
            'action' => 'Action',
            'status' => 'Status',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'groups' => [
            'name' => 'Name',
            'action-category' => 'Action Category',
            'status' => 'Status',
            'delay-count' => 'Delay Count',
            'delay-unit' => 'Delay Unit',
            'delay-source' => 'Delay Source',
            'associated-model' => 'Associated Model',
            'chaining-type' => 'Chaining Type',
            'decoration-type' => 'Decoration Type',
            'default-user' => 'Default User',
            'creation-date' => 'Creation Date',
            'last-update' => 'Last Update',
        ],

        'filters' => [
            'action' => 'Action',
            'status' => 'Status',
            'has-delay' => 'Has Delay',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Activity type restored',
                    'body' => 'The activity type has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Activity type deleted',
                    'body' => 'The activity type has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Activity type force deleted',
                    'body' => 'The activity type has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Activity types restored',
                    'body' => 'The activity types has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Activity types deleted',
                    'body' => 'The activity types has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Activity types force deleted',
                    'body' => 'The activity types has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'General Information',

                'entries' => [
                    'name' => 'Activity Type',
                    'name-tooltip' => 'Enter the official activity type name',
                    'action' => 'Action',
                    'default-user' => 'Default User',
                    'plugin' => 'Plugin',
                    'summary' => 'Summary',
                    'note' => 'Note',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Information',

                'entries' => [
                    'delay-count' => 'Delay Count',
                    'delay-unit' => 'Delay Unit',
                    'delay-form' => 'Delay Form',
                    'delay-form-helper-text' => 'Source of delay calculation',
                ],
            ],

            'advanced-information' => [
                'title' => 'Advanced Information',

                'entries' => [
                    'icon' => 'Icon',
                    'decoration-type' => 'Decoration Type',
                    'chaining-type' => 'Chaining Type',
                    'suggest' => 'Suggest',
                    'trigger' => 'Trigger',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status & Configuration',

                'entries' => [
                    'status' => 'Status',
                    'keep-done-activities' => 'Keep Done Activities',
                ],
            ],
        ],
    ],
];
