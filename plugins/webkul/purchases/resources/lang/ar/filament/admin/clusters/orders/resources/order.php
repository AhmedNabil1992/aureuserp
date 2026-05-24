<?php

return [
    'global-search' => [
        'vendor'    => 'المورد',
        'reference' => 'المرجع',
        'amount'    => 'المبلغ',
    ],
=======

>>>>>>> upstream/master
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'عام',

                'fields' => [
                    'vendor'                   => 'المورد',
                    'vendor-reference'         => 'مرجع المورد',
                    'vendor-reference-tooltip' => 'رقم مرجع أمر البيع أو العرض المقدم من المورد. يُستخدم للمطابقة عند استلام المنتجات، حيث يُدرج هذا المرجع عادةً في أمر تسليم المورد.',
                    'agreement'                => 'الاتفاقية',
                    'currency'                 => 'العملة',
                    'confirmation-date'        => 'تاريخ التأكيد',
                    'order-deadline'           => 'الموعد النهائي للطلب',
                    'expected-arrival'         => 'تاريخ الوصول المتوقع',
                    'confirmed-by-vendor'      => 'مؤكد من المورد',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'المنتجات',

                'repeater' => [
                    'products' => [
                        'title'            => 'المنتجات',
                        'add-product-line' => 'إضافة منتج',

                        'fields' => [
                            'product'             => 'المنتج',
                            'expected-arrival'    => 'تاريخ الوصول المتوقع',
                            'quantity'            => 'الكمية',
                            'received'            => 'المستلم',
                            'billed'              => 'المفوتر',
                            'unit'                => 'الوحدة',
                            'packaging-qty'       => 'كمية التغليف',
                            'packaging'           => 'التغليف',
                            'taxes'               => 'الضرائب',
                            'discount-percentage' => 'الخصم (%)',
                            'unit-price'          => 'سعر الوحدة',
                            'amount'              => 'المبلغ',
                        ],
                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'لا يمكن تقليل الكمية',
                                'body'  => 'لا يمكنك تقليل الكمية إلى أقل من الكمية المستلمة (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'الكمية تتجاوز حد الطلب الشامل',
                                'body'  => 'كمية المنتج (:product_qty) تتجاوز الكمية المتاحة (:available_qty) من الطلب الشامل.',
                            ],
                        ],

                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'لا يمكن تقليل الكمية',
                                'body'  => 'لا يمكنك تقليل الكمية إلى أقل من الكمية المستلمة (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'الكمية تتجاوز حد الطلب الشامل',
                                'body'  => 'كمية المنتج (:product_qty) تتجاوز الكمية المتاحة (:available_qty) من الطلب الشامل.',
                            ],
                        ],

                        'columns' => [
                            'product'             => 'المنتج',
                            'expected-arrival'    => 'تاريخ الوصول المتوقع',
                            'quantity'            => 'الكمية',
                            'received'            => 'المستلم',
                            'billed'              => 'المفوتر',
                            'unit'                => 'الوحدة',
                            'packaging-qty'       => 'كمية التغليف',
                            'packaging'           => 'التغليف',
                            'taxes'               => 'الضرائب',
                            'discount-percentage' => 'الخصم (%)',
                            'unit-price'          => 'سعر الوحدة',
                            'amount'              => 'المبلغ',
                        ],
