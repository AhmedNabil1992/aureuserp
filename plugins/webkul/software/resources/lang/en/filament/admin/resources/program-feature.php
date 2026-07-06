<?php

return [
    'navigation' => [
        'label' => 'Program Features',
    ],

    'form' => [
        'fields' => [
            'subscription_type' => 'Subscription Type',
            'service_product' => 'Service Product',
        ],
        'helper_text' => [
            'subscription_type' => 'When billing, this feature generates an invoice line and a subscription of this type.',
            'service_product' => 'Product service line that will be added to the invoice.',
        ],
    ],

    'table' => [
        'columns' => [
            'program' => 'Program',
            'subscription_type' => 'Subscription Type',
            'service_product' => 'Service Product',
        ],
    ],
];
