<?php

return [
    'navigation' => [
        'title' => 'المنتجات',
        'group' => 'المخزون',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'receive-from' => 'الاستلام من',
                    'contact' => 'جهة الاتصال',
                    'delivery-address' => 'عنوان التسليم',
                    'operation-type' => 'نوع العملية',
                    'source-location' => 'الموقع المصدر',
                    'destination-location' => 'الموقع الوجهة',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'العمليات',

                'fields' => [
                    'product' => 'المنتج',
                    'final-location' => 'الموقع النهائي',
                    'description' => 'الوصف',
                    'scheduled-at' => 'مجدول في',
                    'deadline' => 'الموعد النهائي',
                    'packaging' => 'التغليف',
                    'demand' => 'الطلب',
                    'quantity' => 'الكمية',
                    'unit' => 'الوحدة',
                    'picked' => 'تم الالتقاط',

                    'lines' => [
                        'modal-heading' => 'إدارة حركات المخزون',
                        'add-line' => 'إضافة سطر',

                        'fields' => [
                            'lot' => 'رقم الدفعة/التسلسلي',
                            'pick-from' => 'الالتقاط من',
                            'location' => 'تخزين إلى',
                            'package' => 'طرد الوجهة',
                            'quantity' => 'الكمية',
                            'uom' => 'وحدة القياس',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'إضافي',

                'fields' => [
                    'responsible' => 'المسؤول',
                    'shipping-policy' => 'سياسة الشحن',
                    'shipping-policy-hint-tooltip' => 'تحدد ما إذا كان يجب تسليم البضائع جزئياً أو دفعة واحدة.',
                    'scheduled-at' => 'مجدول في',
                    'scheduled-at-hint-tooltip' => 'وقت الجدولة لمعالجة الجزء الأول من الشحنة. تعيين قيمة هنا يدوياً سيطبقها كتاريخ متوقع لجميع حركات المخزون.',
                    'source-document' => 'المستند المصدر',
                    'source-document-hint-tooltip' => 'مرجع المستند',
                ],
            ],

            'note' => [
                'title' => 'ملاحظة',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference' => 'المرجع',
            'from' => 'من',
            'to' => 'إلى',
            'contact' => 'جهة الاتصال',
            'responsible' => 'المسؤول',
            'scheduled-at' => 'مجدول في',
            'deadline' => 'الموعد النهائي',
            'closed-at' => 'تاريخ الإغلاق',
            'source-document' => 'المستند المصدر',
            'operation-type' => 'نوع العملية',
            'company' => 'الشركة',
            'state' => 'الحالة',
            'deleted-at' => 'تاريخ الحذف',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'state' => 'الحالة',
            'source-document' => 'المستند المصدر',
            'operation-type' => 'نوع العملية',
            'schedule-at' => 'مجدول في',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'name' => 'الاسم',
            'state' => 'الحالة',
            'partner' => 'الشريك',
            'responsible' => 'المسؤول',
            'owner' => 'المالك',
            'source-location' => 'الموقع المصدر',
            'destination-location' => 'الموقع الوجهة',
            'deadline' => 'الموعد النهائي',
            'scheduled-at' => 'مجدول في',
            'closed-at' => 'تاريخ الإغلاق',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
            'company' => 'الشركة',
            'creator' => 'المنشئ',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',
                'entries' => [
                    'contact' => 'جهة الاتصال',
                    'operation-type' => 'نوع العملية',
                    'source-location' => 'الموقع المصدر',
                    'destination-location' => 'الموقع الوجهة',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'العمليات',
                'entries' => [
                    'product' => 'المنتج',
                    'final-location' => 'الموقع النهائي',
                    'description' => 'الوصف',
                    'scheduled-at' => 'مجدول في',
                    'deadline' => 'الموعد النهائي',
                    'packaging' => 'التغليف',
                    'demand' => 'الطلب',
                    'quantity' => 'الكمية',
                    'unit' => 'الوحدة',
                    'picked' => 'تم الالتقاط',
                ],
            ],
            'additional' => [
                'title' => 'معلومات إضافية',
                'entries' => [
                    'responsible' => 'المسؤول',
                    'shipping-policy' => 'سياسة الشحن',
                    'scheduled-at' => 'مجدول في',
                    'source-document' => 'المستند المصدر',
                ],
            ],
            'note' => [
                'title' => 'ملاحظة',
            ],
        ],
    ],

    'tabs' => [
        'todo' => 'قيد التنفيذ',
        'my' => 'تحويلاتي',
        'starred' => 'مميز',
        'draft' => 'مسودة',
        'waiting' => 'بانتظار',
        'ready' => 'جاهز',
        'done' => 'منجز',
        'canceled' => 'ملغي',
    ],
];
