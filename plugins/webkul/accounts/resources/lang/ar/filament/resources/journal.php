<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Journal Entries',

                'field-set' => [
                    'accounting-information' => [
                        'title' => 'Accounting Information',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Dedicated Credit Note Sequence',
                            'dedicated-payment-sequence' => 'Dedicated Payment Sequence',
                            'sort-code-placeholder' => 'Enter the journal code',
                            'sort-code' => 'Sort',
                            'currency' => 'Currency',
                            'color' => 'Color',
                        ],
                    ],
                    'bank-account-number' => [
                        'title' => 'Bank Account Number',
                    ],
                ],
            ],
            'incoming-payments' => [
                'title' => 'Incoming Payments',

                'fields' => [
                    'relation-notes' => 'Relation Notes',
                    'relation-notes-placeholder' => 'Enter any relation details',
                ],
            ],
            'outgoing-payments' => [
                'title' => 'Outgoing Payments',

                'fields' => [
                    'relation-notes' => 'Relation Notes',
                    'relation-notes-placeholder' => 'Enter any relation details',
                ],
            ],
            'advanced-settings' => [
                'title' => 'Advanced Settings',
                'fields' => [
                    'allowed-accounts' => 'Allowed Accounts',
                    'control-access' => 'Control Access',
                    'payment-communication' => 'Payment Communication',
                    'auto-check-on-post' => 'Auto Check on Post',
                    'communication-type' => 'Communication Type',
                    'communication-standard' => 'Communication Standard',
                ],
            ],
            'form' => [
                'tabs' => [
                    'journal-entries' => [
                        'title' => 'قوائم اليومية',

                        'field-set' => [
                            'accounting-information' => [
                                'title' => 'معلومات محاسبية',
                                'fields' => [
                                    'dedicated-credit-note-sequence' => 'تسلسل إشعارات الدائن المخصص',
                                    'dedicated-payment-sequence' => 'تسلسل المدفوعات المخصص',
                                    'sort-code-placeholder' => 'أدخل رمز الدفتر',
                                    'sort-code' => 'الرمز',
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
                            'relation-notes-placeholder' => 'أدخل أي تفاصيل عن العلاقة',
                        ],
                    ],
                    'outgoing-payments' => [
                        'title' => 'المدفوعات الصادرة',

                        'fields' => [
                            'relation-notes' => 'ملاحظات العلاقة',
                            'relation-notes-placeholder' => 'أدخل أي تفاصيل عن العلاقة',
                        ],
                    ],
                    'advanced-settings' => [
                        'title' => 'إعدادات متقدمة',
                        'fields' => [
                            'allowed-accounts' => 'الحسابات المسموح بها',
                            'control-access' => 'التحكم بالوصول',
                            'payment-communication' => 'اتصال الدفع',
                            'auto-check-on-post' => 'التحقق تلقائياً عند النشر',
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
                    'created-by' => 'أنشأ بواسطة',
                    'status' => 'الحالة',
                ],

                'actions' => [
                    'delete' => [
                        'notification' => [
                            'title' => 'تم حذف القيد',
                            'body' => 'تم حذف القيد بنجاح.',
                        ],
                    ],
                ],

                'bulk-actions' => [
                    'delete' => [
                        'notification' => [
                            'title' => 'تم حذف دفاتر اليومية',
                            'body' => 'تم حذف دفاتر اليومية بنجاح.',
                        ],
                    ],
                ],
            ],

            'infolist' => [
                'tabs' => [
                    'journal-entries' => [
                        'title' => 'قوائم اليومية',

                        'field-set' => [
                            'accounting-information' => [
                                'title' => 'معلومات محاسبية',
                                'entries' => [
                                    'dedicated-credit-note-sequence' => 'تسلسل إشعارات الدائن المخصص',
                                    'dedicated-payment-sequence' => 'تسلسل المدفوعات المخصص',
                                    'sort-code-placeholder' => 'أدخل رمز الدفتر',
                                    'sort-code' => 'الرمز',
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
                            'relation-notes-placeholder' => 'أدخل أي تفاصيل عن العلاقة',
                        ],
                    ],
                    'outgoing-payments' => [
                        'title' => 'المدفوعات الصادرة',

                        'entries' => [
                            'relation-notes' => 'ملاحظات العلاقة',
                            'relation-notes-placeholder' => 'أدخل أي تفاصيل عن العلاقة',
                        ],
                    ],
                    'advanced-settings' => [
                        'title' => 'إعدادات متقدمة',
                        'entries' => [
                            'allowed-accounts' => 'الحسابات المسموح بها',
                            'control-access' => 'التحكم بالوصول',
                            'payment-communication' => 'اتصال الدفع',
                            'auto-check-on-post' => 'التحقق تلقائياً عند النشر',
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
        ],
    ],


];
