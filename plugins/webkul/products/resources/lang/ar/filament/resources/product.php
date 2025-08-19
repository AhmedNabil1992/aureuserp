<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name' => 'الاسم',
                    'name-placeholder' => 'مثال: تي-شيرت',
                    'description' => 'الوصف',
                    'tags' => 'العلامات',
                ],
            ],

            'images' => [
                'title' => 'الصور',
            ],

            'inventory' => [
                'title' => 'المخزون',

                'fields' => [],

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'الخدمات اللوجستية',

                        'fields' => [
                            'weight' => 'الوزن',
                            'volume' => 'الحجم',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'fields' => [
                    'type' => 'النوع',
                    'reference' => 'الرقم المرجعي',
                    'barcode' => 'الباركود',
                    'category' => 'الفئة',
                    'company' => 'الشركة',
                ],
            ],

            'pricing' => [
                'title' => 'التسعير',

                'fields' => [
                    'price' => 'السعر',
                    'cost' => 'التكلفة',
                ],
            ],

            'additional' => [
                'title' => 'إضافي',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'الاسم',
            'variants' => 'الاختلافات',
            'images' => 'الصور',
            'type' => 'النوع',
            'reference' => 'الرقم المرجعي',
            'responsible' => 'المسؤول',
            'barcode' => 'الباركود',
            'category' => 'الفئة',
            'company' => 'الشركة',
            'price' => 'السعر',
            'cost' => 'التكلفة',
            'on-hand' => 'المتوفر',
            'tags' => 'العلامات',
            'deleted-at' => 'تاريخ الحذف',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
        ],

        'groups' => [
            'type' => 'النوع',
            'category' => 'الفئة',
            'created-at' => 'تاريخ الإنشاء',
        ],

        'filters' => [
            'name' => 'الاسم',
            'type' => 'النوع',
            'reference' => 'الرقم المرجعي',
            'barcode' => 'الباركود',
            'category' => 'الفئة',
            'company' => 'الشركة',
            'price' => 'السعر',
            'cost' => 'التكلفة',
            'is-favorite' => 'مفضل',
            'weight' => 'الوزن',
            'volume' => 'الحجم',
            'tags' => 'العلامات',
            'responsible' => 'المسؤول',
            'created-at' => 'تاريخ الإنشاء',
            'updated-at' => 'تاريخ التحديث',
            'creator' => 'المنشئ',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المنتج',
                    'body' => 'تم استعادة المنتج بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المنتج',
                    'body' => 'تم حذف المنتج بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم الحذف الإجباري للمنتج',
                        'body' => 'تم الحذف الإجباري للمنتج بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف المنتج',
                        'body' => 'لا يمكن حذف المنتج لأنه قيد الاستخدام حاليًا.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'طباعة الملصقات',

                'form' => [
                    'fields' => [
                        'quantity' => 'عدد الملصقات',
                        'format' => 'التنسيق',

                        'format-options' => [
                            'dymo' => 'Dymo',
                            '2x7_price' => '2x7 مع السعر',
                            '4x7_price' => '4x7 مع السعر',
                            '4x12' => '4x12',
                            '4x12_price' => '4x12 مع السعر',
                        ],
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'تم استعادة المنتجات',
                    'body' => 'تم استعادة المنتجات بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف المنتجات',
                    'body' => 'تم حذف المنتجات بنجاح.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'تم الحذف الإجباري للمنتجات',
                        'body' => 'تم الحذف الإجباري للمنتجات بنجاح.',
                    ],

                    'error' => [
                        'title' => 'تعذر حذف المنتجات',
                        'body' => 'لا يمكن حذف المنتجات لأنها قيد الاستخدام حاليًا.',
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
                    'name' => 'الاسم',
                    'name-placeholder' => 'مثل. تي شيرت',
                    'description' => 'الوصف',
                    'tags' => 'العلامات',
                ],
            ],

            'images' => [
                'title' => 'الصور',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'الإعدادات',

                'entries' => [
                    'type' => 'النوع',
                    'reference' => 'المرجع',
                    'barcode' => 'الباركود',
                    'category' => 'الفئة',
                    'company' => 'الشركة',
                ],
            ],

            'pricing' => [
                'title' => 'التسعير',

                'entries' => [
                    'price' => 'السعر',
                    'cost' => 'التكلفة',
                ],
            ],

            'inventory' => [
                'title' => 'المخزون',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'اللوجستيات',

                        'entries' => [
                            'weight' => 'الوزن',
                            'volume' => 'الحجم',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'معلومات السجل',

                'entries' => [
                    'created-at' => 'تاريخ الإنشاء',
                    'created-by' => 'أنشأ بواسطة',
                    'updated-at' => 'تاريخ التحديث',
                ],
            ],
        ],
    ],
];
