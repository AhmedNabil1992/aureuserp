<?php

return [
    'title' => 'الموردون',

    'navigation' => [
        'title' => 'الموردون',
    ],

    'form' => [
        'fields' => [
            'sales-person' => 'مندوب المبيعات',
            'payment-terms' => 'شروط الدفع',
            'payment-method' => 'طريقة الدفع',
            'fiscal-position' => 'الوضع الضريبي',
            'purchase' => 'المشتريات',
            'fiscal-information' => 'المعلومات الضريبية',
        ],
        'tabs' => [
            'invoicing' => [
                'title' => 'الفواتير',
                'fields' => [
                    'customer-invoices' => 'فواتير العملاء',
                    'invoice-sending-method' => 'طريقة إرسال الفاتورة',
                    'invoice-edi-format-store' => 'تنسيق الفاتورة الإلكترونية',
                    'peppol-eas' => 'عنوان Peppol',
                    'endpoint' => 'نقطة النهاية',
                    'auto-post-bills' => 'ترحيل الفواتير تلقائياً',
                    'automation' => 'الأتمتة',
                    'ignore-abnormal-invoice-amount' => 'تجاهل مبلغ الفاتورة غير الطبيعي',
                    'ignore-abnormal-invoice-date' => 'تجاهل تاريخ الفاتورة غير الطبيعي',
                ],
            ],
            'internal-notes' => [
                'title' => 'ملاحظات داخلية',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sales-person' => 'مندوب المبيعات',
            'payment-terms' => 'شروط الدفع',
            'payment-method' => 'طريقة الدفع',
            'fiscal-position' => 'الوضع الضريبي',
            'purchase' => 'المشتريات',
            'fiscal-information' => 'المعلومات الضريبية',
        ],
        'tabs' => [
            'invoicing' => [
                'title' => 'الفواتير',
                'entries' => [
                    'customer-invoices' => 'فواتير العملاء',
                    'invoice-sending-method' => 'طريقة إرسال الفاتورة',
                    'invoice-edi-format-store' => 'تنسيق الفاتورة الإلكترونية',
                    'peppol-eas' => 'عنوان Peppol',
                    'endpoint' => 'نقطة النهاية',
                    'auto-post-bills' => 'ترحيل الفواتير تلقائياً',
                    'automation' => 'الأتمتة',
                    'ignore-abnormal-invoice-amount' => 'تجاهل مبلغ الفاتورة غير الطبيعي',
                    'ignore-abnormal-invoice-date' => 'تجاهل تاريخ الفاتورة غير الطبيعي',
                ],
            ],
            'internal-notes' => [
                'title' => 'ملاحظات داخلية',
            ],
        ],
    ],
];
