<?php

return [
    'title' => 'خطة الإعلان',

    'form' => [
        'sections' => [
            'planned' => [
                'title' => 'المخطط (بداية الشهر)',
            ],
            'actual' => [
                'title' => 'الفعلي (نهاية الشهر)',
            ],
        ],
        'fields' => [
            'planned-budget'      => 'الميزانية المخططة',
            'planned-reach'       => 'الوصول المخطط',
            'planned-messages'    => 'الرسائل المخططة',
            'planned-conversions' => 'التحويلات المخططة',
            'actual-budget'       => 'الميزانية الفعلية',
            'actual-reach'        => 'الوصول الفعلي',
            'actual-messages'     => 'الرسائل الفعلية',
            'actual-conversions'  => 'التحويلات الفعلية',
            'actual-leads'        => 'العملاء المحتملون المُولَّدون',
            'notes'               => 'ملاحظات',
        ],
    ],

    'table' => [
        'columns' => [
            'planned-budget'      => 'الميزانية المخططة',
            'actual-budget'       => 'الميزانية الفعلية',
            'planned-messages'    => 'الرسائل المخططة',
            'actual-messages'     => 'الرسائل الفعلية',
            'planned-conversions' => 'التحويلات المخططة',
            'actual-conversions'  => 'التحويلات الفعلية',
            'actual-leads'        => 'العملاء المحتملون المُولَّدون',
        ],
    ],
];
