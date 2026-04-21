<?php

return [
    'navigation' => [
        'title' => 'Dynamic Clients',
        'group' => 'Network',
    ],

    'model-label'        => 'Dynamic Client',
    'plural-model-label' => 'Dynamic Clients',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Client Information',
                'fields' => [
                    'cloud'         => 'Cloud Name',
                    'name'          => 'Client Name',
                    'nasidentifier' => 'NAS Identifier',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                                              => 'ID',
            'cloud'                                           => 'Cloud',
            'name'                                            => 'Name',
            'nasidentifier'                                   => 'NAS Identifier',
            'last_contact'                                    => 'Last Contact',
            'last_contact_ip'                                 => 'Last Contact IP',
            'zero_ip'                                         => 'Zero IP',
            'picture'                                         => 'Picture',
            'picture_uploaded'                                => 'Picture Uploaded',
            'no_picture'                                      => 'No Picture',
            'last_contact_less_than_1_day'                    => 'Last Contact Less Than 1 Day',
            'last_contact_more_than_1_day_less_than_1_week'   => 'Last Contact More Than 1 Day and Less Than 1 Week',
            'last_contact_more_than_1_week_less_than_1_month' => 'Last Contact More Than 1 Week and Less Than 1 Month',
            'last_contact_more_than_1_month'                  => 'Last Contact More Than 1 Month',
        ],
    ],
];
