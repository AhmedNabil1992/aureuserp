<?php

return [
    'title' => 'دفتر اليومية',

    'navigation' => [
        'title' => 'دفتر اليومية',
        'group' => 'المحاسبة',
    ],

    'global-search' => [
        'name' => 'الاسم',
        'code' => 'الرمز',
    ],

    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'قيود اليومية',

                'field-set' => [
                    'accounting-information' => [
                        'title' => 'معلومات المحاسبة',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'تسلسل إشعار الدائن المخصص',
                            'dedicated-payment-sequence' => 'تسلسل الدفع المخصص',
                            'sort-code-placeholder' => 'أدخل رمز دفتر اليومية',
                            'sort-code' => 'ترتيب',
                            'currency' => 'العملة',
                            'color' => 'اللون',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'رقم الحساب البنكي',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'المدفوعات الواردة',

                'fields' => [
                    'relation-notes' => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل تفاصيل العلاقة',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'المدفوعات الصادرة',

                'fields' => [
                    'relation-notes' => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل تفاصيل العلاقة',
                ],
            ],
            'advanced-settings' => [
                'title' => 'إعدادات متقدمة',
                'fields' => [
                    'allowed-accounts' => 'الحسابات المسموح بها',
                    'control-access' => 'التحكم في الوصول',
                    'payment-communication' => 'اتصال الدفع',
                    'auto-check-on-post' => 'التحقق التلقائي عند النشر',
                    'communication-type' => 'نوع الاتصال',
                    'communication-standard' => 'معيار الاتصال',
                ],
            ],
        ],

        'general' => [
            'title' => 'معلومات عامة',

            'fields' => [
                'name' => 'الاسم',
                'type' => 'النوع',
                'company' => 'الشركة',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'type' => 'النوع',
            'code' => 'الرمز',
            'currency' => 'العملة',
            'created-by' => 'أنشئ بواسطة',
            'status' => 'الحالة',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الإنكوترم',
                    'body' => 'تم حذف الإنكوترم بنجاح.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف دفتر اليومية',
                    'body' => 'تم حذف دفتر اليومية بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'قيود اليومية',

                'field-set' => [
                    'accounting-information' => [
                        'title' => 'معلومات المحاسبة',
                        'entries' => [
                            'dedicated-credit-note-sequence' => 'تسلسل إشعار الدائن المخصص',
                            'dedicated-payment-sequence' => 'تسلسل الدفع المخصص',
                            'sort-code-placeholder' => 'أدخل رمز دفتر اليومية',
                            'sort-code' => 'ترتيب',
                            'currency' => 'العملة',
                            'color' => 'اللون',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'رقم الحساب البنكي',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'المدفوعات الواردة',

                'entries' => [
                    'relation-notes' => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل تفاصيل العلاقة',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'المدفوعات الصادرة',

                'entries' => [
                    'relation-notes' => 'ملاحظات العلاقة',
                    'relation-notes-placeholder' => 'أدخل تفاصيل العلاقة',
                ],
            ],
            'advanced-settings' => [
                'title' => 'إعدادات متقدمة',
                'entries' => [
                    'allowed-accounts' => 'الحسابات المسموح بها',
                    'control-access' => 'التحكم في الوصول',
                    'payment-communication' => 'اتصال الدفع',
                    'auto-check-on-post' => 'التحقق التلقائي عند النشر',
                    'communication-type' => 'نوع الاتصال',
                    'communication-standard' => 'معيار الاتصال',
                ],
            ],
        ],

        'general' => [
            'title' => 'معلومات عامة',

            'entries' => [
                'name' => 'الاسم',
                'type' => 'النوع',
                'company' => 'الشركة',
            ],
        ],
    ],

];
