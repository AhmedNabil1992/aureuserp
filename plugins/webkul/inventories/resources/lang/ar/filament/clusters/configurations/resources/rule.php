<?php

return [
    'navigation' => [
        'title' => 'القواعد',
        'group' => 'إدارة المستودعات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'name' => 'الاسم',
                    'action' => 'الإجراء',
                    'operation-type' => 'نوع العملية',
                    'source-location' => 'الموقع المصدر',
                    'destination-location' => 'الموقع الوجهة',
                    'supply-method' => 'طريقة التوريد',
                    'supply-method-hint-tooltip' => 'أخذ من المخزون: يتم جلب المنتجات مباشرة من المخزون المتوفر في الموقع المصدر.<br/>تشغيل قاعدة أخرى: يتجاهل النظام المخزون المتوفر ويبحث عن قاعدة مخزون لتزويد الموقع المصدر.<br/>أخذ من المخزون، إذا غير متوفر، تشغيل قاعدة أخرى: يتم أخذ المنتجات أولاً من المخزون المتوفر، وإذا لم يتوفر، يطبق النظام قاعدة مخزون لجلب المنتجات إلى الموقع المصدر.',
                    'automatic-move' => 'نقل تلقائي',
                    'automatic-move-hint-tooltip' => 'عملية يدوية: ينشئ حركة مخزون منفصلة بعد الحركة الحالية.<br/>تلقائي بدون إضافة خطوة: يستبدل الموقع في الحركة الأصلية مباشرة دون إضافة خطوة إضافية.',

                    'action-information' => [
                        'pull' => 'عند الحاجة إلى المنتجات في <b>:sourceLocation</b>، يتم إنشاء :operation من <b>:destinationLocation</b> لتلبية الطلب.',
                        'push' => 'عند وصول المنتجات إلى <b>:sourceLocation</b>، يتم إنشاء <b>:operation</b> لنقلها إلى <b>:destinationLocation</b>.',
                    ],
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'partner-address' => 'عنوان الشريك',
                    'partner-address-hint-tooltip' => 'العنوان الذي يجب تسليم البضائع إليه. اختياري.',
                    'lead-time' => 'مدة التنفيذ (أيام)',
                    'lead-time-hint-tooltip' => 'سيتم حساب تاريخ النقل المتوقع باستخدام هذه المدة.',
                ],

                'fieldsets' => [
                    'applicability' => [
                        'title' => 'القابلية للتطبيق',

                        'fields' => [
                            'route' => 'المسار',
                            'company' => 'الشركة',
                        ],
                    ],

                    'propagation' => [
                        'title' => 'الانتشار',

                        'fields' => [
                            'propagation-procurement-group' => 'انتشار مجموعة الشراء',
                            'propagation-procurement-group-hint-tooltip' => 'إذا تم تحديده، فإن إلغاء الحركة التي أنشأتها هذه القاعدة سيؤدي أيضاً إلى إلغاء الحركة التالية.',
                            'cancel-next-move' => 'إلغاء الحركة التالية',
                            'warehouse-to-propagate' => 'المستودع للانتشار',
                            'warehouse-to-propagate-hint-tooltip' => 'المستودع المخصص للحركة أو الشراء الذي قد يختلف عن المستودع الذي تنطبق عليه هذه القاعدة (مثلاً لقواعد إعادة التوريد من مستودع آخر).',
                        ],
                    ],
                ],

            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'action' => 'الإجراء',
            'source-location' => 'الموقع المصدر',
            'destination-location' => 'الموقع الوجهة',
            'route' => 'المسار',
            'deleted-at' => 'تاريخ الحذف',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'action' => 'الإجراء',
            'source-location' => 'الموقع المصدر',
            'destination-location' => 'الموقع الوجهة',
            'route' => 'المسار',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'action' => 'الإجراء',
            'source-location' => 'الموقع المصدر',
            'destination-location' => 'الموقع الوجهة',
            'route' => 'المسار',
            'company' => 'الشركة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث القاعدة',
                    'body' => 'تم تحديث القاعدة بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة القاعدة',
                    'body' => 'تم استعادة القاعدة بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف القاعدة',
                    'body' => 'تم حذف القاعدة بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف القاعدة نهائياً',
                        'body' => 'تم حذف القاعدة نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف القاعدة',
                        'body' => 'لا يمكن حذف القاعدة لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة القواعد',
                    'body' => 'تم استعادة القواعد بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف القواعد',
                    'body' => 'تم حذف القواعد بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف القواعد نهائياً',
                        'body' => 'تم حذف القواعد نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف القواعد',
                        'body' => 'لا يمكن حذف القواعد لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'تفاصيل القاعدة',

                'description' => [
                    'pull' => 'عند الحاجة إلى المنتجات في <b>:sourceLocation</b>، يتم إنشاء <b>:operation</b> من <b>:destinationLocation</b> لتلبية الطلب.',
                    'push' => 'عند وصول المنتجات إلى <b>:sourceLocation</b>، يتم إنشاء <b>:operation</b> لنقلها إلى <b>:destinationLocation</b>.',
                ],

                'entries' => [
                    'name' => 'اسم القاعدة',
                    'action' => 'الإجراء',
                    'operation-type' => 'نوع العملية',
                    'source-location' => 'الموقع المصدر',
                    'destination-location' => 'الموقع الوجهة',
                    'route' => 'المسار',
                    'company' => 'الشركة',
                    'partner-address' => 'عنوان الشريك',
                    'lead-time' => 'مدة التنفيذ',
                    'action-information' => 'معلومات الإجراء',
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
    ],
];
