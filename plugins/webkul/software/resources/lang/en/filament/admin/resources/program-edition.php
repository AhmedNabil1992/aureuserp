<?php

return [
    'navigation' => [
        'label' => 'Program Editions',
    ],

    'form' => [
        'fields' => [
            'linked_variant' => 'Linked Variant (Required for billing)',
            'legacy_product_link' => 'Legacy Product Link (Optional)',
        ],
        'helper_text' => [
            'linked_variant' => 'Choose the exact product variant that represents this edition.',
            'legacy_product_link' => 'Legacy field kept for backward compatibility.',
        ],
    ],

    'table' => [
        'columns' => [
            'program' => 'Program',
            'variant' => 'Variant',
        ],
    ],
];
