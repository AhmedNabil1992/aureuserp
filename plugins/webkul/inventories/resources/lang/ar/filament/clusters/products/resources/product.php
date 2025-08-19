<?php

return [
    'navigation' => [
        'title' => 'المنتجات',
        'group' => 'المخزون',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'المخزون',

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'التتبع',

                        'fields' => [
                            'track-inventory' => 'تتبع المخزون',
                            'track-inventory-hint-tooltip' => 'المنتج القابل للتخزين هو الذي يتطلب إدارة المخزون.',
                            'track-by' => 'تتبع بواسطة',
                            'expiration-date' => 'تاريخ الانتهاء',
                            'expiration-date-hint-tooltip' => 'إذا تم تحديده، يمكنك تحديد تواريخ انتهاء للمنتج وأرقام الدفعة/التسلسلي المرتبطة به.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'العمليات',

                        'fields' => [
                            'routes' => 'المسارات',
                            'routes-hint-tooltip' => 'بناءً على الوحدات المثبتة، يتيح لك هذا الإعداد تحديد مسار المنتج مثل الشراء أو التصنيع أو إعادة التوريد عند الطلب.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'اللوجستيات',

                        'fields' => [
                            'responsible' => 'المسؤول',
                            'responsible-hint-tooltip' => 'مدة التسليم (بالأيام) تمثل الفترة الموعودة بين تأكيد طلب البيع وتسليم المنتج.',
                            'weight' => 'الوزن',
                            'volume' => 'الحجم',
                            'sale-delay' => 'مدة التسليم للعميل (أيام)',
                            'sale-delay-hint-tooltip' => 'مدة التسليم (بالأيام) تمثل الفترة الموعودة بين تأكيد طلب البيع وتسليم المنتج.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'التتبع',

                        'fields' => [
                            'expiration-date' => 'تاريخ الانتهاء (أيام)',
                            'expiration-date-hint-tooltip' => 'إذا تم تحديده، يمكنك تعيين تواريخ انتهاء للمنتج وأرقام الدفعة/التسلسلي المرتبطة به.',
                            'best-before-date' => 'أفضل قبل تاريخ (أيام)',
                            'best-before-date-hint-tooltip' => 'عدد الأيام قبل تاريخ الانتهاء عندما يبدأ المنتج في التدهور، لكنه لا يزال صالحاً للاستخدام. يتم حساب ذلك بناءً على رقم الدفعة/التسلسلي.',
                            'removal-date' => 'تاريخ الإزالة (أيام)',
                            'removal-date-hint-tooltip' => 'عدد الأيام قبل تاريخ الانتهاء عندما يجب إزالة المنتج من المخزون. يتم حساب ذلك بناءً على رقم الدفعة/التسلسلي.',
                            'alert-date' => 'تاريخ التنبيه (أيام)',
                            'alert-date-hint-tooltip' => 'عدد الأيام قبل تاريخ الانتهاء عندما يجب إصدار تنبيه لرقم الدفعة/التسلسلي. يتم حساب ذلك بناءً على رقم الدفعة/التسلسلي.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'إضافي',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'المخزون',

                'entries' => [
                ],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'التتبع',

                        'entries' => [
                            'track-inventory' => 'تتبع المخزون',
                            'track-by' => 'تتبع بواسطة',
                            'expiration-date' => 'تاريخ الانتهاء',
                        ],
                    ],

                    'operation' => [
                        'title' => 'العمليات',

                        'entries' => [
                            'routes' => 'المسارات',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'اللوجستيات',

                        'entries' => [
                            'responsible' => 'المسؤول',
                            'weight' => 'الوزن',
                            'volume' => 'الحجم',
                            'sale-delay' => 'مدة التسليم للعميل (أيام)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'التتبع',

                        'entries' => [
                            'expiration-date' => 'تاريخ الانتهاء (أيام)',
                            'best-before-date' => 'أفضل قبل تاريخ (أيام)',
                            'removal-date' => 'تاريخ الإزالة (أيام)',
                            'alert-date' => 'تاريخ التنبيه (أيام)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
