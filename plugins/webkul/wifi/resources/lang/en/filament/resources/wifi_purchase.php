<?php

return [
    'navigation' => [
        'title' => 'Wi-Fi Purchases',
        'group' => 'Network',
    ],

    'model-label'        => 'Wi-Fi Purchase',
    'plural-model-label' => 'Wi-Fi Purchases',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Purchase Information',
                'fields' => [
                    'partner_id'       => 'Customer',
                    'wifi_package_id'  => 'Wi-Fi Package',
                    'cloud_id'         => 'Cloud',
                ],
                'helper' => [
                    'partner_id'      => 'Select the customer purchasing the Wi-Fi package.',
                    'wifi_package_id' => 'Select the Wi-Fi package being purchased.',
                    'cloud_id'        => 'If the customer has one assigned cloud, it will be selected automatically.',
                ],
            ],
        ],
        'buttons' => [
            'new-purchase' => 'New Wi-Fi Purchase',
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'partner'            => 'Customer',
            'package'            => 'Package',
            'service_product'    => 'Service Product',
            'invoice'            => 'Invoice',
            'cloud'              => 'Cloud',
            'quantity'           => 'Quantity',
            'generated_quantity' => 'Generated',
            'remaining_quantity' => 'Remaining',
            'updated_at'         => 'Updated At',
        ],
    ],

    'messages' => [
        'purchase_success'   => 'Wi-Fi purchase successful.',
        'select_package'     => 'Please select a Wi-Fi package.',
        'select_cloud'       => 'Please select a cloud.',
        'select_customer'    => 'Please select a customer.',
        'package_currency'   => 'The selected package has no currency. Please update the package first.',
        'cloud_assigned'     => 'The selected cloud is already assigned to this customer.',
        'no_sales'           => 'No sales journal found. Please configure at least one sales journal first.',
    ],
];
