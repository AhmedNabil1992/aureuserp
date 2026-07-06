<?php

return [
    'navigation' => [
        'label' => 'Programs',
    ],

    'form' => [
        'fields' => [
            'name' => 'Name',
            'slug' => 'Slug',
            'base_service_product' => 'Base Service Product',
            'description' => 'Description',
            'installation_notes' => 'Installation Notes',
            'active' => 'Active',
        ],
        'helper_text' => [
            'base_service_product' => 'Main product for this software program.',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Name',
            'description' => 'Description',
            'slug' => 'Slug',
            'base_product' => 'Base Product',
            'installation_notes' => 'Installation Notes',
            'creator' => 'Creator',
            'created_at' => 'Created At',
            'active' => 'Active',
            'updated_at' => 'Updated At',
        ],
    ],
];
