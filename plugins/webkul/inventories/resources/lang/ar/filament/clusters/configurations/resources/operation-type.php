<?php

return [
    'navigation' => [
        'title' => 'أنواع العمليات',
        'group' => 'إدارة المستودعات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type' => 'نوع المشغل',
                    'operator-type-placeholder' => 'مثال: الاستقبالات',
                ],
            ],

            'applicable-on' => [
                'title' => 'ينطبق على',
                'description' => 'حدد الأماكن التي يمكن اختيار هذا المسار فيها.',

                'fields' => [
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'operator-type' => 'نوع المشغل',
                    'sequence-prefix' => 'بادئة التسلسل',
                    'generate-shipping-labels' => 'إنشاء ملصقات الشحن',
                    'warehouse' => 'المستودع',
                    'show-reception-report' => 'عرض تقرير الاستقبال عند التحقق',
                    'show-reception-report-hint-tooltip' => 'إذا تم تحديده، سيعرض النظام تلقائياً تقرير الاستقبال عند التحقق، بشرط وجود حركات للتخصيص.',
                    'company' => 'الشركة',
                    'return-type' => 'نوع الإرجاع',
                    'create-backorder' => 'إنشاء طلب خلفي',
                    'move-type' => 'نوع الحركة',
                    'move-type-hint-tooltip' => 'ما لم يتم تحديده بواسطة المستند المصدر، سيستخدم هذا كسياسة الالتقاط الافتراضية لهذا النوع من العمليات.',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'الدفعات/الأرقام التسلسلية',

                        'fields' => [
                            'create-new' => 'إنشاء جديد',
                            'create-new-hint-tooltip' => 'إذا تم تحديده، سيفترض النظام أنك تنوي إنشاء دفعات/أرقام تسلسلية جديدة، ويمكنك إدخالها في حقل نصي.',
                            'use-existing' => 'استخدام الموجود',
                            'use-existing-hint-tooltip' => 'إذا تم تحديده، يمكنك اختيار الدفعات/الأرقام التسلسلية أو عدم تعيين أي منها. يسمح ذلك بإنشاء المخزون بدون دفعة أو بدون قيود على الدفعة المستخدمة.',
                        ],
                    ],

                    'locations' => [
                        'title' => 'المواقع',

                        'fields' => [
                            'source-location' => 'الموقع المصدر',
                            'source-location-hint-tooltip' => 'يستخدم كموقع مصدر افتراضي عند إنشاء هذه العملية يدوياً. يمكن تغييره لاحقاً، وقد تعين المسارات موقعاً افتراضياً مختلفاً.',
                            'destination-location' => 'الموقع الوجهة',
                            'destination-location-hint-tooltip' => 'يستخدم كموقع وجهة افتراضي للعمليات التي تم إنشاؤها يدوياً. يمكن تعديله لاحقاً، وقد تعين المسارات موقعاً افتراضياً مختلفاً.',
                        ],
                    ],

                    'packages' => [
                        'title' => 'الطرود',

                        'fields' => [
                            'show-entire-package' => 'نقل الطرد بالكامل',
                            'show-entire-package-hint-tooltip' => 'إذا تم تحديده، يمكنك نقل الطرود بالكامل.',
                        ],
                    ],
                ],
            ],

            'hardware' => [
                'title' => 'الأجهزة',

                'fieldsets' => [
                    'print-on-validation' => [
                        'title' => 'الطباعة عند التحقق',

                        'fields' => [
                            'delivery-slip' => 'إيصال التسليم',
                            'delivery-slip-hint-tooltip' => 'إذا تم تحديده، سيطبع النظام تلقائياً إيصال التسليم عند التحقق من الالتقاط.',

                            'return-slip' => 'إيصال الإرجاع',
                            'return-slip-hint-tooltip' => 'إذا تم تحديده، سيطبع النظام تلقائياً إيصال الإرجاع عند التحقق من الالتقاط.',

                            'product-labels' => 'ملصقات المنتج',
                            'product-labels-hint-tooltip' => 'إذا تم تحديده، سيطبع النظام تلقائياً ملصقات المنتج عند التحقق من الالتقاط.',

                            'lots-labels' => 'ملصقات الدفعة/الرقم التسلسلي',
                            'lots-labels-hint-tooltip' => 'إذا تم تحديده، سيطبع النظام تلقائياً ملصقات الدفعة/الرقم التسلسلي عند التحقق من الالتقاط.',

                            'reception-report' => 'تقرير الاستقبال',
                            'reception-report-hint-tooltip' => 'إذا تم تحديده، سيطبع النظام تلقائياً تقرير الاستقبال عند التحقق من الالتقاط ويحتوي على حركات مخصصة.',

                            'reception-report-labels' => 'ملصقات تقرير الاستقبال',
                            'reception-report-labels-hint-tooltip' => 'إذا تم تحديده، سيطبع النظام تلقائياً ملصقات تقرير الاستقبال عند التحقق من الالتقاط.',

                            'package-content' => 'محتوى الطرد',
                            'package-content-hint-tooltip' => 'إذا تم تحديده، سيطبع النظام تلقائياً تفاصيل الطرد ومحتوياته عند التحقق من الالتقاط.',
                        ],
                    ],

                    'print-on-pack' => [
                        'title' => 'الطباعة عند "وضع في الطرد"',

                        'fields' => [
                            'package-label' => 'ملصق الطرد',
                            'package-label-hint-tooltip' => 'إذا تم تحديده، سيطبع النظام تلقائياً ملصق الطرد عند استخدام زر "وضع في الطرد".',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'warehouse' => 'المستودع',
            'company' => 'الشركة',
            'deleted-at' => 'تاريخ الحذف',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'type' => 'النوع',
            'warehouse' => 'المستودع',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'type' => 'النوع',
            'warehouse' => 'المستودع',
            'company' => 'الشركة',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة نوع العملية',
                    'body' => 'تم استعادة نوع العملية بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف نوع العملية',
                    'body' => 'تم حذف نوع العملية بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف نوع العملية نهائياً',
                        'body' => 'تم حذف نوع العملية نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف نوع العملية',
                        'body' => 'لا يمكن حذف نوع العملية لأنه قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة أنواع العمليات',
                    'body' => 'تم استعادة أنواع العمليات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف أنواع العمليات',
                    'body' => 'تم حذف أنواع العمليات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف أنواع العمليات نهائياً',
                        'body' => 'تم حذف أنواع العمليات نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف أنواع العمليات',
                        'body' => 'لا يمكن حذف أنواع العمليات لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'empty-actions' => [
            'create' => [
                'label' => 'إنشاء نوع عملية',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'معلومات عامة',

                'entries' => [
                    'name' => 'الاسم',
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'created-by' => 'أنشئ بواسطة',
                    'created-at' => 'تاريخ الإنشاء',
                    'last-updated' => 'آخر تحديث',
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title' => 'عام',

                'entries' => [
                    'type' => 'نوع العملية',
                    'sequence_code' => 'رمز التسلسل',
                    'print_label' => 'طباعة الملصق',
                    'warehouse' => 'المستودع',
                    'reservation_method' => 'طريقة الحجز',
                    'auto_show_reception_report' => 'عرض تقرير الاستقبال تلقائياً',
                    'company' => 'الشركة',
                    'return_operation_type' => 'نوع عملية الإرجاع',
                    'create_backorder' => 'إنشاء طلب خلفي',
                    'move_type' => 'نوع الحركة',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'الدفعات',

                        'entries' => [
                            'use_create_lots' => 'استخدام إنشاء دفعات',
                            'use_existing_lots' => 'استخدام دفعات موجودة',
                        ],
                    ],

                    'locations' => [
                        'title' => 'المواقع',

                        'entries' => [
                            'source_location' => 'الموقع المصدر',
                            'destination_location' => 'الموقع الوجهة',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'الأجهزة',

                'fieldsets' => [
                    'print_on_validation' => [
                        'title' => 'الطباعة عند التحقق',

                        'entries' => [
                            'auto_print_delivery_slip' => 'طباعة إيصال التسليم تلقائياً',
                            'auto_print_return_slip' => 'طباعة إيصال الإرجاع تلقائياً',
                            'auto_print_product_labels' => 'طباعة ملصقات المنتج تلقائياً',
                            'auto_print_lot_labels' => 'طباعة ملصقات الدفعة تلقائياً',
                            'auto_print_reception_report' => 'طباعة تقرير الاستقبال تلقائياً',
                            'auto_print_reception_report_labels' => 'طباعة ملصقات تقرير الاستقبال تلقائياً',
                            'auto_print_packages' => 'طباعة الطرود تلقائياً',
                        ],
                    ],

                    'print_on_pack' => [
                        'title' => 'الطباعة عند التعبئة',

                        'entries' => [
                            'auto_print_package_label' => 'طباعة ملصق الطرد تلقائياً',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
