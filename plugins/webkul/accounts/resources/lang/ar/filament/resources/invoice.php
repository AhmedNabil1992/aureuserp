<?php

return [
    'title' => 'Invoice',

    'navigation' => [
        'title' => 'Invoices',
        'group' => 'Invoices',
    ],

    'global-search' => [
        'number' => 'Number',
        'customer' => 'Customer',
        'invoice-date' => 'Invoice Date',
        'invoice-due-date' => 'Invoice Due Date',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'General',
                'fields' => [
                    'customer-invoice' => 'Customer Invoice',
                    'customer' => 'Customer',
                    'invoice-date' => 'Invoice Date',
                    'due-date' => 'Due Date',
                    'payment-term' => 'Payment Term',
                    'title' => 'الفواتير',

                    'navigation' => [
                        'title' => 'الفواتير',
                        'group' => 'الفواتير',
                    ],

                    'global-search' => [
                        'number' => 'الرقم',
                        'customer' => 'العميل',
                        'invoice-date' => 'تاريخ الفاتورة',
                        'invoice-due-date' => 'تاريخ استحقاق الفاتورة',
                    ],

                    'form' => [
                        'section' => [
                            'general' => [
                                'title' => 'عام',
                                'fields' => [
                                    'customer-invoice' => 'فاتورة العميل',
                                    'customer' => 'العميل',
                                    'invoice-date' => 'تاريخ الفاتورة',
                                    'due-date' => 'تاريخ الاستحقاق',
                                    'payment-term' => 'مدة الدفع',
                                ],
                            ],
                        ],

                        'tabs' => [
                            'invoice-lines' => [
                                'title' => 'بنود الفاتورة',

                                'repeater' => [
                                    'products' => [
                                        'title' => 'المنتجات',
                                        'add-product' => 'إضافة منتج',

                                        'fields' => [
                                            'product' => 'المنتج',
                                            'quantity' => 'الكمية',
                                            'unit' => 'الوحدة',
                                            'taxes' => 'الضرائب',
                                            'discount-percentage' => 'نسبة الخصم',
                                            'unit-price' => 'سعر الوحدة',
                                            'sub-total' => 'المجموع الفرعي',
                                        ],
                                    ],
                                ],
                            ],

                            'other-information' => [
                                'title' => 'معلومات أخرى',
                                'fieldset' => [
                                    'invoice' => [
                                        'title' => 'الفاتورة',
                                        'fields' => [
                                            'customer-reference' => 'مرجع العميل',
                                            'sales-person' => 'مندوب المبيعات',
                                            'payment-reference' => 'مرجع الدفع',
                                            'recipient-bank' => 'بنك المستلم',
                                            'delivery-date' => 'تاريخ التسليم',
                                        ],
                                    ],

                                    'accounting' => [
                                        'title' => 'المحاسبة',

                                        'fieldset' => [
                                            'incoterm' => 'قواعد التسليم (Incoterm)',
                                            'incoterm-location' => 'موقع قواعد التسليم',
                                            'payment-method' => 'طريقة الدفع',
                                            'auto-post' => 'نشر تلقائي',
                                            'checked' => 'تم التحقق',
                                        ],
                                    ],

                                    'additional-information' => [
                                        'title' => 'معلومات إضافية',
                                        'fields' => [
                                            'company' => 'الشركة',
                                            'currency' => 'العملة',
                                        ],
                                    ],

                                    'marketing' => [
                                        'title' => 'التسويق',
                                        'fields' => [
                                            'campaign' => 'الحملة',
                                            'medium' => 'الوسيلة',
                                            'source' => 'المصدر',
                                        ],
                                    ],
                                ],
                            ],

                            'term-and-conditions' => [
                                'title' => 'الشروط والأحكام',
                            ],
                        ],
                    ],

                    'table' => [
                        'total' => 'الإجمالي',
                        'columns' => [
                            'number' => 'الرقم',
                            'state' => 'الحالة',
                            'customer' => 'العميل',
                            'invoice-date' => 'تاريخ الفاتورة',
                            'checked' => 'تم التحقق',
                            'accounting-date' => 'تاريخ المحاسبة',
                            'due-date' => 'تاريخ الاستحقاق',
                            'source-document' => 'المستند المصدر',
                            'reference' => 'المرجع',
                            'sales-person' => 'مندوب المبيعات',
                            'tax-excluded' => 'المبلغ قبل الضريبة',
                            'tax' => 'الضريبة',
                            'total' => 'الإجمالي',
                            'amount-due' => 'المبلغ المستحق',
                            'invoice-currency' => 'عملة الفاتورة',
                        ],

                        'groups' => [
                            'name' => 'الاسم',
                            'invoice-partner-display-name' => 'اسم عرض شريك الفاتورة',
                            'invoice-date' => 'تاريخ الفاتورة',
                            'checked' => 'تم التحقق',
                            'date' => 'التاريخ',
                            'invoice-due-date' => 'تاريخ استحقاق الفاتورة',
                            'invoice-origin' => 'مصدر الفاتورة',
                            'sales-person' => 'مندوب المبيعات',
                            'currency' => 'العملة',
                            'created-at' => 'تاريخ الإنشاء',
                            'updated-at' => 'آخر تعديل',
                        ],

                        'filters' => [
                            'number' => 'الرقم',
                            'invoice-partner-display-name' => 'اسم عرض شريك الفاتورة',
                            'invoice-date' => 'تاريخ الفاتورة',
                            'invoice-due-date' => 'تاريخ استحقاق الفاتورة',
                            'invoice-origin' => 'مصدر الفاتورة',
                            'reference' => 'المرجع',
                            'created-at' => 'تاريخ الإنشاء',
                            'updated-at' => 'آخر تعديل',
                        ],

                        'actions' => [
                            'delete' => [
                                'notification' => [
                                    'title' => 'تم حذف الفاتورة',
                                    'body' => 'تم حذف الفاتورة بنجاح.',
                                ],
                            ],
                        ],

                        'bulk-actions' => [
                            'delete' => [
                                'notification' => [
                                    'title' => 'تم حذف الفواتير',
                                    'body' => 'تم حذف الفواتير بنجاح.',
                                ],
                            ],
                        ],
                    ],

                    'infolist' => [
                        'section' => [
                            'general' => [
                                'title' => 'عام',
                                'entries' => [
                                    'customer-invoice' => 'فاتورة العميل',
                                    'customer' => 'العميل',
                                    'invoice-date' => 'تاريخ الفاتورة',
                                    'due-date' => 'تاريخ الاستحقاق',
                                    'payment-term' => 'مدة الدفع',
                                ],
                            ],
                        ],

                        'tabs' => [
                            'invoice-lines' => [
                                'title' => 'بنود الفاتورة',

                                'repeater' => [
                                    'products' => [
                                        'entries' => [
                                            'product' => 'المنتج',
                                            'quantity' => 'الكمية',
                                            'unit' => 'وحدة القياس',
                                            'taxes' => 'الضرائب',
                                            'discount-percentage' => 'نسبة الخصم',
                                            'unit-price' => 'سعر الوحدة',
                                            'sub-total' => 'المجموع الفرعي',
                                            'total' => 'الإجمالي',
                                        ],
                                    ],
                                ],
                            ],

                            'other-information' => [
                                'title' => 'معلومات أخرى',
                                'fieldset' => [
                                    'invoice' => [
                                        'title' => 'الفاتورة',
                                        'entries' => [
                                            'customer-reference' => 'مرجع العميل',
                                            'sales-person' => 'مندوب المبيعات',
                                            'payment-reference' => 'مرجع الدفع',
                                            'recipient-bank' => 'بنك المستلم',
                                            'delivery-date' => 'تاريخ التسليم',
                                        ],
                                    ],

                                    'accounting' => [
                                        'title' => 'المحاسبة',

                                        'fieldset' => [
                                            'incoterm' => 'قواعد التسليم (Incoterm)',
                                            'incoterm-location' => 'موقع قواعد التسليم',
                                            'payment-method' => 'طريقة الدفع',
                                            'auto-post' => 'نشر تلقائي',
                                            'checked' => 'تم التحقق',
                                        ],
                                    ],

                                    'additional-information' => [
                                        'title' => 'معلومات إضافية',
                                        'entries' => [
                                            'company' => 'الشركة',
                                            'currency' => 'العملة',
                                        ],
                                    ],

                                    'marketing' => [
                                        'title' => 'التسويق',
                                        'entries' => [
                                            'campaign' => 'الحملة',
                                            'medium' => 'الوسيلة',
                                            'source' => 'المصدر',
                                        ],
                                    ],
                                ],
                            ],

                            'term-and-conditions' => [
                                'title' => 'الشروط والأحكام',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

];
