<?php

return [
    'global-search' => [
        'code' => 'الرمز',
        'type' => 'النوع',
    ],
    'form' => [
        'sections' => [
            'fields' => [
                'code'          => 'الرمز',
                'account-name'  => 'اسم الحساب',
                'accounting'    => 'المحاسبة',
                'account-type'  => 'نوع الحساب',
                'default-taxes' => 'الضرائب الافتراضية',
                'tags'          => 'الوسوم',
                'journals'      => 'اليوميات',
                'currency'      => 'العملة',
                'deprecated'    => 'مهمل',
                'reconcile'     => 'تسوية',
                'non-trade'     => 'غير تجاري',
                'companies'     => 'الشركات',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'         => 'الرمز',
            'account-name' => 'اسم الحساب',
            'account-type' => 'نوع الحساب',
            'currency'     => 'العملة',
            'deprecated'   => 'مهمل',
            'reconcile'    => 'تسوية',
            'non-trade'    => 'غير تجاري',
            'journals'     => 'اليوميات',
        ],

        'grouping' => [
            'account-type' => 'نوع الحساب',
        ],

        'filters' => [
            'account-type'     => 'نوع الحساب',
            'allow-reconcile'  => 'السماح بالتسوية',
            'currency'         => 'العملة',
            'account-journals' => 'اليوميات',
            'non-trade'        => 'غير تجاري',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تعديل الحساب',
                    'body'  => 'تم تعديل الحساب بنجاح.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title'   => 'تم حذف الحسابات',
                'body'    => 'تم حذف الحسابات بنجاح.',
                'success' => [
                    'title' => 'تم حذف الحساب',
                    'body'  => 'تم حذف الحساب بنجاح.',
                ],

                'error' => [
                    'title' => 'فشل حذف الحساب',
                    'body'  => 'لا يمكن حذف الحساب لأنه يحتوي على قيود يومية مرتبطة.',
                ],
            ],
        ],
    ],
    'bulk-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'تم حذف الحسابات',
                    'body'  => 'تم حذف الحسابات بنجاح.',
                    'error' => [
                        'title' => 'فشل حذف الحسابات',
                        'body'  => 'لا يمكن حذف الحسابات لأنها تحتوي على قيود يومية مرتبطة.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'          => 'الرمز',
                'account-name'  => 'اسم الحساب',
                'accounting'    => 'المحاسبة',
                'account-type'  => 'نوع الحساب',
                'default-taxes' => 'الضرائب الافتراضية',
                'tags'          => 'الوسوم',
                'journals'      => 'اليوميات',
                'currency'      => 'العملة',
                'deprecated'    => 'مهمل',
                'reconcile'     => 'تسوية',
                'non-trade'     => 'غير تجاري',
            ],
        ],
    ],
];
