<?php

return [
    'navigation' => [
        'title' => 'قوائم المواد',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'البيانات الأساسية',
                'fields' => [
                    'product'         => 'المنتج',
                    'type'            => 'النوع',
                    'quantity'        => 'الكمية',
                    'uom'             => 'وحدة القياس',
                    'reference'       => 'المرجع',
                    'company'         => 'الشركة',
                    'source_location' => 'لوكيشن الخصم',
                    'notes'           => 'ملاحظات',
                ],
            ],
            'components' => [
                'title'  => 'المكونات',
                'fields' => [
                    'component' => 'المكوّن',
                    'quantity'  => 'الكمية',
                    'uom'       => 'وحدة القياس',
                    'notes'     => 'ملاحظات',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'product'         => 'المنتج',
            'type'            => 'النوع',
            'quantity'        => 'الكمية',
            'uom'             => 'وحدة القياس',
            'components'      => 'عدد المكونات',
            'company'         => 'الشركة',
            'source_location' => 'لوكيشن الخصم',
            'updated_at'      => 'آخر تحديث',
        ],
    ],
];
