<?php

return [
    'navigation' => [
        'group' => 'Settings',
        'title' => 'Cities',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'country'            => 'Country',
                    'governorate'        => 'Governorate',
                    'governorate-helper' => 'Select country first',
                    'name'               => 'City Name',
                    'name-ar'            => 'City Name (Arabic)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'country'        => 'Country',
            'name'           => 'City',
            'name-ar'        => 'City (Arabic)',
            'governorate'    => 'Governorate',
            'governorate-ar' => 'Governorate (Arabic)',
            'created-at'     => 'Created At',
        ],
        'filters' => [
            'location'    => 'Location',
            'country'     => 'Country',
            'governorate' => 'Governorate',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'City deleted',
                    'body'  => 'The city has been deleted successfully.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Cities deleted',
                    'body'  => 'The selected cities have been deleted successfully.',
                ],
            ],
        ],
    ],

    'pages' => [
        'list-city' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Add City',
                ],
            ],
        ],
        'create-city' => [
            'notification' => [
                'title' => 'City created',
                'body'  => 'The city has been created successfully.',
            ],
        ],
        'edit-city' => [
            'notification' => [
                'title' => 'City updated',
                'body'  => 'The city has been updated successfully.',
            ],
            'header-actions' => [
                'delete' => [
                    'notification' => [
                        'title' => 'City deleted',
                        'body'  => 'The city has been deleted successfully.',
                    ],
                ],
            ],
        ],
    ],
];
