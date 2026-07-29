<?php

return [
    'title' => 'المخزون',
    'table' => [
        'columns' => [
            'category'  => 'المجموعة',
            'barcode'   => 'كود الصنف',
            'item_name' => 'اسم الصنف',
            'quantity'  => 'الكمية',
            'min_alert' => 'الحد الأدنى',
        ],
        'filters' => [
            'low_stock'      => 'نواقص المخزون',
            'quantity_range' => 'فلترة الكمية',
            'ranges' => [
                'zero'     => 'صفر',
                'positive' => 'أكبر من صفر',
                'negative' => 'أقل من صفر',
            ],
        ],
        'empty_state' => [
            'heading'     => 'لا يوجد بيانات مخزون',
            'description' => 'لم يتم العثور على أي أصناف مخزون للعرض.',
        ],
    ],
];
