<?php

return [
    'form' => [
        'fields' => [
            'tax-source' => 'مصدر الضريبة',
            'tax-destination' => 'وجهة الضريبة',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source' => 'مصدر الضريبة',
            'tax-destination' => 'وجهة الضريبة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث إعداد الضريبة',
                    'body' => 'تم تحديث إعداد الضريبة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف إعداد الضريبة',
                    'body' => 'تم حذف إعداد الضريبة بنجاح.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء إعداد الضريبة',
                    'body' => 'تم إنشاء إعداد الضريبة بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'tax-source' => 'مصدر الضريبة',
            'tax-destination' => 'وجهة الضريبة',
        ],
    ],
];
