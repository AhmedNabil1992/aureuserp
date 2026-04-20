<?php

return [
    'navigation' => [
        'title' => 'العملاء المحتملون',
        'group' => 'العملاء المحتملون',
    ],

    'model-label'        => 'عميل محتمل',
    'plural-model-label' => 'العملاء المحتملون',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات العميل المحتمل',
                'fields' => [
                    'name'         => 'الاسم الكامل',
                    'phone'        => 'الهاتف',
                    'email'        => 'البريد الإلكتروني',
                    'company-name' => 'اسم الشركة',
                    'service-type' => 'الخدمة المطلوبة',
                    'notes'        => 'ملاحظات',
                ],
            ],
            'settings' => [
                'title'  => 'الإعدادات',
                'fields' => [
                    'status'      => 'الحالة',
                    'source'      => 'المصدر',
                    'temperature' => 'الدرجة الحرارية',
                    'assigned-to' => 'مسنَد إلى',
                    'campaign'    => 'الحملة',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'الاسم',
            'phone'              => 'الهاتف',
            'service-type'       => 'الخدمة',
            'status'             => 'الحالة',
            'temperature'        => 'الدرجة الحرارية',
            'source'             => 'المصدر',
            'interactions-count' => 'التفاعلات',
            'next-follow-up'     => 'المتابعة القادمة',
            'assigned-to'        => 'مسنَد إلى',
            'campaign'           => 'الحملة',
            'created-at'         => 'تاريخ الإنشاء',
        ],
        'filters' => [
            'status'      => 'الحالة',
            'source'      => 'المصدر',
            'temperature' => 'الدرجة الحرارية',
            'assigned-to' => 'مسنَد إلى',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'معلومات العميل المحتمل',
                'entries' => [
                    'name'         => 'الاسم الكامل',
                    'phone'        => 'الهاتف',
                    'email'        => 'البريد الإلكتروني',
                    'company-name' => 'اسم الشركة',
                    'service-type' => 'الخدمة المطلوبة',
                    'notes'        => 'ملاحظات',
                ],
            ],
            'settings' => [
                'title'   => 'التفاصيل',
                'entries' => [
                    'status'      => 'الحالة',
                    'temperature' => 'الدرجة الحرارية',
                    'source'      => 'المصدر',
                    'assigned-to' => 'مسنَد إلى',
                    'campaign'    => 'الحملة',
                    'creator'     => 'أنشئ بواسطة',
                    'created-at'  => 'تاريخ الإنشاء',
                ],
            ],
        ],
    ],
];
