<?php

return [
    'navigation' => [
        'title' => 'إعادة التوريد',
        'group' => 'المشتريات',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product' => 'المنتج',
            'location' => 'الموقع',
            'route' => 'المسار',
            'vendor' => 'المورد',
            'trigger' => 'المشغل',
            'on-hand' => 'المتوفر',
            'min' => 'الحد الأدنى',
            'max' => 'الحد الأقصى',
            'multiple-quantity' => 'كمية متعددة',
            'to-order' => 'للطلب',
            'uom' => 'وحدة القياس',
            'company' => 'الشركة',
        ],

        'groups' => [
            'location' => 'الموقع',
            'product' => 'المنتج',
            'category' => 'الفئة',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'إضافة إعادة توريد',

                'notification' => [
                    'title' => 'تمت إضافة إعادة التوريد',
                    'body' => 'تمت إضافة إعادة التوريد بنجاح.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'إعادة التوريد موجودة مسبقاً',
                        'body' => 'توجد إعادة توريد مسبقاً لهذا التكوين. يرجى تحديث إعادة التوريد الحالية بدلاً من ذلك.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];
