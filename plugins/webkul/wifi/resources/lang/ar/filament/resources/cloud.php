<?php

return [
    'navigation' => [
        'title'   => 'السحب السحابية',
        'group'   => 'شبكة واي فاي',
        'refresh' => 'تحديث',
    ],

    'model-label'        => 'سحابة',
    'plural-model-label' => 'السحب السحابية',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'معلومات السحابة',
                'fields' => [
                    'id'       => 'المعرف',
                    'name'     => 'الاسم',
                    'created'  => 'تاريخ الإنشاء',
                    'modified' => 'تاريخ التعديل',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'       => 'المعرف',
            'name'     => 'الاسم',
            'created'  => 'تاريخ الإنشاء',
            'modified' => 'تاريخ التعديل',
        ],
    ],
];
