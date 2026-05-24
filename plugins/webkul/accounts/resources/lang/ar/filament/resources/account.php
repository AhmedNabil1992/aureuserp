<?php

return [
    'global-search' => [
        'code' => 'الرمز',
        'type' => 'النوع',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code'                   => 'الرمز',
                'account-name'           => 'اسم الحساب',
                'accounting'             => 'المحاسبة',
                'account-type'           => 'نوع الحساب',
                'parent-account'         => 'الحساب الرئيسي',
                'parent-account-helper'  => 'اختر حسابًا موجودًا لجعل هذا حسابًا فرعيًا.',
                'default-taxes'          => 'الضرائب الافتراضية',
                'tags'                   => 'الوسوم',
                'journals'               => 'اليوميات',
                'journals-helper'        => 'يتم اقتراحها تلقائيًا بناءً على نوع الحساب المحدد. يمكنك تعديل الاختيار.',
                'currency'               => 'العملة',
                'deprecated'             => 'مهمل',
                'reconcile'              => 'تسوية',
                'non-trade'              => 'غير تجاري',
                'companies'              => 'الشركات',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'           => 'الرمز',
            'account-name'   => 'اسم الحساب',
            'account-type'   => 'نوع الحساب',
            'parent-account' => 'الحساب الرئيسي',
            'currency'       => 'العملة',
            'journals'       => 'اليوميات',
            'reconcile'      => 'تسوية',
        ],

        'grouping' => [
            'account-type' => 'نوع الحساب',
>>>>>>> upstream/master
        ],

        'filters' => [
            'account-type'     => 'نوع الحساب',
