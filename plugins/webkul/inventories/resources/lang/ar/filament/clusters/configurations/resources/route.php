<?php

return [
    'navigation' => [
        'title' => 'المسارات',
        'group' => 'إدارة المستودعات',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'route' => 'المسار',
                    'route-placeholder' => 'مثال: استقبال من خطوتين',
                    'company' => 'الشركة',
                ],
            ],

            'applicable-on' => [
                'title' => 'ينطبق على',
                'description' => 'اختر المواقع التي يمكن تطبيق هذا المسار عليها.',

                'fields' => [
                    'products' => 'المنتجات',
                    'products-hint-tooltip' => 'إذا تم تحديده، سيكون هذا المسار متاحاً للاختيار في المنتج.',
                    'product-categories' => 'فئات المنتجات',
                    'product-categories-hint-tooltip' => 'إذا تم تحديده، سيكون هذا المسار متاحاً للاختيار في فئة المنتج.',
                    'warehouses' => 'المستودعات',
                    'warehouses-hint-tooltip' => 'عند تعيين مستودع لهذا المسار، سيعتبر المسار الافتراضي للمنتجات التي تمر عبر هذا المستودع.',
                    'packaging' => 'التغليف',
                    'packaging-hint-tooltip' => 'إذا تم تحديده، سيكون هذا المسار متاحاً للاختيار في التغليف.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route' => 'المسار',
            'company' => 'الشركة',
            'deleted-at' => 'تاريخ الحذف',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'filters' => [
            'company' => 'الشركة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث المسار',
                    'body' => 'تم تحديث المسار بنجاح.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المسار',
                    'body' => 'تم استعادة المسار بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المسار',
                    'body' => 'تم حذف المسار بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف المسار نهائياً',
                        'body' => 'تم حذف المسار نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف المسار',
                        'body' => 'لا يمكن حذف المسار لأنه قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المسارات',
                    'body' => 'تم استعادة المسارات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المسارات',
                    'body' => 'تم حذف المسارات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم حذف المسارات نهائياً',
                        'body' => 'تم حذف المسارات نهائياً بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف المسارات',
                        'body' => 'لا يمكن حذف المسارات لأنها قيد الاستخدام حالياً.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'entries' => [
                    'route' => 'المسار',
                    'route-placeholder' => 'مثال: استقبال من خطوتين',
                    'company' => 'الشركة',
                ],
            ],

            'applicable-on' => [
                'title' => 'ينطبق على',
                'description' => 'حدد الأماكن التي يمكن اختيار هذا المسار فيها.',

                'entries' => [
                    'products' => 'المنتجات',
                    'products-hint-tooltip' => 'إذا تم تحديده، سيكون هذا المسار متاحاً للاختيار في المنتج.',
                    'product-categories' => 'فئات المنتجات',
                    'product-categories-hint-tooltip' => 'إذا تم تحديده، سيكون هذا المسار متاحاً للاختيار في فئة المنتج.',
                    'warehouses' => 'المستودعات',
                    'warehouses-hint-tooltip' => 'عند تعيين مستودع لهذا المسار، سيعتبر المسار الافتراضي للمنتجات التي تمر عبر هذا المستودع.',
                    'packaging' => 'التغليف',
                    'packaging-hint-tooltip' => 'إذا تم تحديده، سيكون هذا المسار متاحاً للاختيار في التغليف.',
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
