<?php

return [
    'navigation' => [
        'title' => 'الحملات',
        'group' => 'التسويق',
    ],

    'model-label'        => 'حملة',
    'plural-model-label' => 'الحملات',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'تفاصيل الحملة',
                'fields' => [
                    'name'        => 'اسم الحملة',
                    'platform'    => 'المنصة',
                    'month'       => 'الشهر',
                    'description' => 'الوصف',
                ],
            ],
            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'status'      => 'الحالة',
                    'assigned-to' => 'مسنَد إلى',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'اسم الحملة',
            'platform'         => 'المنصة',
            'month'            => 'الشهر',
            'status'           => 'الحالة',
            'planned-budget'   => 'الميزانية المخططة',
            'actual-budget'    => 'الميزانية الفعلية',
            'planned-messages' => 'الرسائل المخططة',
            'actual-messages'  => 'الرسائل الفعلية',
            'actual-leads'     => 'عملاء خطة الإعلان',
            'leads-count'      => 'العملاء المحتملون الفعليون',
            'assigned-to'      => 'مسنَد إلى',
            'created-at'       => 'تاريخ الإنشاء',
        ],
        'filters' => [
            'platform' => 'المنصة',
            'status'   => 'الحالة',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'تفاصيل الحملة',
                'entries' => [
                    'name'        => 'اسم الحملة',
                    'platform'    => 'المنصة',
                    'month'       => 'الشهر',
                    'description' => 'الوصف',
                ],
            ],
            'plan' => [
                'title'   => 'خطة الإعلان',
                'entries' => [
                    'planned-budget'      => 'الميزانية المخططة',
                    'actual-budget'       => 'الميزانية الفعلية',
                    'planned-reach'       => 'الوصول المخطط',
                    'actual-reach'        => 'الوصول الفعلي',
                    'planned-messages'    => 'الرسائل المخططة',
                    'actual-messages'     => 'الرسائل الفعلية',
                    'planned-conversions' => 'التحويلات المخططة',
                    'actual-conversions'  => 'التحويلات الفعلية',
                    'actual-leads'        => 'العملاء المحتملون المُولَّدون',
                    'notes'               => 'ملاحظات',
                ],
            ],
            'settings' => [
                'title'   => 'الإعدادات',
                'entries' => [
                    'status'      => 'الحالة',
                    'assigned-to' => 'مسنَد إلى',
                    'creator'     => 'أنشئ بواسطة',
                    'created-at'  => 'تاريخ الإنشاء',
                ],
            ],
        ],
    ],
];
