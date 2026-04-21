<?php

return [
    'navigation' => [
        'title' => 'Wi-Fi Packages',
        'group' => 'Network',
    ],

    'model-label'        => 'Wi-Fi Package',
    'plural-model-label' => 'Wi-Fi Packages',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Package Information',
                'fields' => [
                    'product_id'               => 'Service Product',
                    'product_id_helper_text'   => 'Recommended: keep all Wi-Fi packages linked to a single service product (Wi-Fi Voucher).',
                    'package_type'             => 'Package Type',
                    'package_type_helper_text' => 'Use Unlimited for open validity packages and Limited for time-bound packages.',
                    'currency_id'              => 'Currency',
                    'quantity'                 => 'Cards Per Unit',
                    'amount'                   => 'Sell Amount',
                    'dealer_amount'            => 'Dealer Amount',
                    'is_active'                => 'Active',
                    'description'              => 'Description',
                ],
            ],
        ],
        'buttons' => [
            'new-package' => 'Add Wi-Fi Package',
        ],
    ],

    'table' => [
        'columns' => [
            'product'        => 'Service Product',
            'package_type'   => 'Package Type',
            'currency'       => 'Currency',
            'quantity'       => 'Cards Per Unit',
            'amount'         => 'Sell Amount',
            'dealer_amount'  => 'Dealer Amount',
            'is_active'      => 'Active',
            'updated_at'     => 'Updated At',
        ],
    ],
];
