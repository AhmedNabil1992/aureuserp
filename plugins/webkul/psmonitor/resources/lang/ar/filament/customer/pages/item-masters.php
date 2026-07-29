<?php

return [
    'title' => 'الأصناف',
    'table' => [
        'columns' => [
            'group'           => 'المجموعة',
            'code'            => 'الكود',
            'item_name'       => 'اسم الصنف',
            'item_price'      => 'سعر الصنف',
            'table_price'     => 'سعر الطاولة',
            'direct_price'    => 'السعر المباشر',
            'min_stock_alert' => 'حد التنبيه',
            'is_product'      => 'منتج',
            'is_sales'        => 'مبيعات',
            'is_active'       => 'مفعل',
        ],
        'actions' => [
            'add_item' => 'إضافة صنف',
        ],
        'empty_state' => [
            'heading' => 'لا يوجد أصناف للعرض',
        ],
    ],
];
