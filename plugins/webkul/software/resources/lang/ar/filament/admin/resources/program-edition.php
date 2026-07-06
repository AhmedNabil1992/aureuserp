<?php

return [
    'navigation' => [
        'label' => 'إصدارات البرامج',
    ],

    'form' => [
        'fields' => [
            'linked_variant' => 'المتغير المرتبط (مطلوب للفوترة)',
            'legacy_product_link' => 'ربط المنتج القديم (اختياري)',
        ],
        'helper_text' => [
            'linked_variant' => 'اختر متغير المنتج الذي يمثل هذا الإصدار بدقة.',
            'legacy_product_link' => 'حقل قديم للإبقاء على التوافق الخلفي.',
        ],
    ],

    'table' => [
        'columns' => [
            'program' => 'البرنامج',
            'variant' => 'المتغير',
        ],
    ],
];
