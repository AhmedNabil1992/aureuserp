<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'name' => 'الاسم',
                'tax-type' => 'نوع الضريبة',
                'tax-computation' => 'حساب الضريبة',
                'tax-scope' => 'نطاق الضريبة',
                'status' => 'الحالة',
                'amount' => 'المبلغ',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'خيارات متقدمة',

                    'fields' => [
                        'invoice-label' => 'تسمية الفاتورة',
                        'tax-group' => 'مجموعة الضريبة',
                        'country' => 'البلد',
                        'include-in-price' => 'مُدرج في السعر',
                        'include-base-amount' => 'تأثير على أساس الضرائب التالية',
                        'is-base-affected' => 'تأثر الأساس بالضرائب السابقة',
                    ],
                ],

                'fields' => [
                    'description' => 'الوصف',
                    'legal-notes' => 'ملاحظات قانونية',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'amount-type' => 'نوع المبلغ',
            'company' => 'الشركة',
            'tax-group' => 'مجموعة الضريبة',
            'country' => 'البلد',
            'tax-type' => 'نوع الضريبة',
            'tax-scope' => 'نطاق الضريبة',
            'invoice-label' => 'تسمية الفاتورة',
            'tax-exigibility' => 'استحقاق الضريبة',
            'price-include-override' => 'تجاوز إدراج السعر',
            'amount' => 'المبلغ',
            'status' => 'الحالة',
            'include-base-amount' => 'تضمين المبلغ الأساسي',
            'is-base-affected' => 'هل تأثر الأساس',
        ],

        'groups' => [
            'name' => 'الاسم',
            'company' => 'الشركة',
            'tax-group' => 'مجموعة الضريبة',
            'country' => 'البلد',
            'created-by' => 'أنشأ بواسطة',
            'type-tax-use' => 'نوع استخدام الضريبة',
            'tax-scope' => 'نطاق الضريبة',
            'amount-type' => 'نوع المبلغ',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الضريبة',
                        'body' => 'تم حذف الضريبة بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الضريبة',
                        'body' => 'لا يمكن حذف الضريبة لأنها قيد الاستخدام.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف الضرائب',
                        'body' => 'تم حذف الضرائب بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف الضرائب',
                        'body' => 'لا يمكن حذف الضرائب لأنها قيد الاستخدام.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name' => 'الاسم',
                'tax-type' => 'نوع الضريبة',
                'tax-computation' => 'حساب الضريبة',
                'tax-scope' => 'نطاق الضريبة',
                'status' => 'الحالة',
                'amount' => 'المبلغ',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'خيارات متقدمة',

                    'entries' => [
                        'invoice-label' => 'تسمية الفاتورة',
                        'tax-group' => 'مجموعة الضريبة',
                        'country' => 'البلد',
                        'include-in-price' => 'مُدرج في السعر',
                        'include-base-amount' => 'تضمين المبلغ الأساسي',
                        'is-base-affected' => 'هل تأثر الأساس',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title' => 'الوصف والملاحظات القانونية للفاتورة',
                    'entries' => [
                        'description' => 'الوصف',
                        'legal-notes' => 'ملاحظات قانونية',
                    ],
                ],
            ],
        ],
    ],

];
