<?php

return [
    'title' => 'التفاعلات',

    'form' => [
        'fields' => [
            'type'             => 'النوع',
            'subject'          => 'الموضوع',
            'interaction-date' => 'التاريخ',
            'notes'            => 'ملاحظات',
            'outcome'          => 'النتيجة',
            'next-action'      => 'الإجراء التالي',
            'follow-up-date'   => 'تاريخ المتابعة',
        ],
    ],

    'table' => [
        'columns' => [
            'type'             => 'النوع',
            'subject'          => 'الموضوع',
            'interaction-date' => 'التاريخ',
            'outcome'          => 'النتيجة',
            'follow-up-date'   => 'تاريخ المتابعة',
            'user'             => 'سُجِّل بواسطة',
        ],
        'filters' => [
            'type' => 'النوع',
        ],
    ],
];
