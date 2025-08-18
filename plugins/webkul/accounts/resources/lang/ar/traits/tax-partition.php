<?php

return [
    'form' => [
        'factor-percent' => 'نسبة العامل',
        'factor-ratio' => 'نسبة العامل (نسبة)',
        'repartition-type' => 'نوع التوزيع',
        'document-type' => 'نوع المستند',
        'account' => 'الحساب',
        'tax' => 'الضريبة',
        'tax-closing-entry' => 'قيد إقفال الضريبة',
    ],

    'table' => [
        'columns' => [
            'factor-percent' => 'نسبة العامل(%)',
            'account' => 'الحساب',
            'tax' => 'الضريبة',
            'company' => 'الشركة',
            'repartition-type' => 'نوع التوزيع',
            'document-type' => 'نوع المستند',
            'tax-closing-entry' => 'قيد إقفال الضريبة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث تقسيم الضريبة',
                    'body' => 'تم تحديث تقسيم الضريبة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف شرط تقسيم الضريبة',
                    'body' => 'تم حذف شرط تقسيم الضريبة بنجاح.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء شرط تقسيم الضريبة',
                    'body' => 'تم إنشاء شرط تقسيم الضريبة بنجاح.',
                ],
            ],
        ],
    ],
];
