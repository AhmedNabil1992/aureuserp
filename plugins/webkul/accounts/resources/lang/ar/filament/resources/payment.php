<?php

return [
    'title' => 'المدفوع',

    'navigation' => [
        'title' => 'المدفوعات',
        'group' => 'الفواتير',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type' => 'نوع الدفع',
                'memo' => 'ملاحظات',
                'date' => 'التاريخ',
                'amount' => 'المبلغ',
                'payment-method' => 'طريقة الدفع',
                'customer' => 'العميل',
                'journal' => 'دفتر اليومية',
                'customer-bank-account' => 'حساب العميل البنكي',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'company' => 'الشركة',
            'bank-account-holder' => 'حامل الحساب البنكي',
            'paired-internal-transfer-payment' => 'دفع تحويل داخلي مطابق',
            'payment-method-line' => 'سطر طريقة الدفع',
            'payment-method' => 'طريقة الدفع',
            'currency' => 'العملة',
            'partner' => 'الشريك',
            'outstanding-amount' => 'المبلغ المستحق',
            'destination-account' => 'الحساب المستلم',
            'created-by' => 'أنشئ بواسطة',
            'payment-transaction' => 'معاملة الدفع',
        ],

        'groups' => [
            'name' => 'الاسم',
            'company' => 'الشركة',
            'partner' => 'الشريك',
            'payment-method-line' => 'سطر طريقة الدفع',
            'payment-method' => 'طريقة الدفع',
            'partner-bank-account' => 'حساب بنك الشريك',
            'paired-internal-transfer-payment' => 'دفع تحويل داخلي مطابق',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'آخر تعديل',
        ],

        'filters' => [
            'company' => 'الشركة',
            'customer-bank-account' => 'حساب العميل البنكي',
            'paired-internal-transfer-payment' => 'دفع تحويل داخلي مطابق',
            'payment-method' => 'طريقة الدفع',
            'currency' => 'العملة',
            'partner' => 'الشريك',
            'partner-method-line' => 'سطر طريقة الشريك',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'آخر تعديل',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الدفع',
                    'body' => 'تم حذف الدفع بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المدفوعات',
                    'body' => 'تم حذف المدفوعات بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'payment-information' => [
                'title' => 'معلومات الدفع',
                'entries' => [
                    'state' => 'الحالة',
                    'payment-type' => 'نوع الدفع',
                    'journal' => 'دفتر اليومية',
                    'customer-bank-account' => 'حساب العميل البنكي',
                    'customer' => 'العميل',
                ],
            ],

            'payment-details' => [
                'title' => 'تفاصيل الدفع',
                'entries' => [
                    'amount' => 'المبلغ',
                    'date' => 'التاريخ',
                    'memo' => 'ملاحظات',
                ],
            ],

            'payment-method' => [
                'title' => 'طريقة الدفع',
                'entries' => [
                    'payment-method' => 'طريقة الدفع',
                ],
            ],
        ],
    ],

];

