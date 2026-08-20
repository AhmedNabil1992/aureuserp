<?php

return [
    'navigation' => [
        'label' => 'Program Features',
    ],

    'title' => [
        'index' => 'Program Features',
        'create' => 'Add Program Feature',
        'edit' => 'Edit Program Feature',
    ],

    'form' => [
        'fields' => [
            'subscription_type' => 'Subscription Type',
            'service_product'   => 'Service Product',
        ],
        'helper_text' => [
            'subscription_type' => 'When billing, this feature generates an invoice line and a subscription of this type.',
            'service_product'   => 'Product service line that will be added to the invoice.',
        ],
    ],

    'table' => [
        'columns' => [
            'program'           => 'Program',
            'name'              => 'Name',
            'subscription_type' => 'Subscription Type',
            'service_product'   => 'Service Product',
            'amount'            => 'Amount',
            'created_at'        => 'Created At',
            'updated_at'        => 'Updated At',
        ],
    ],
];
