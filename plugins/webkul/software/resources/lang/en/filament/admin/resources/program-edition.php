<?php

return [
    'navigation' => [
        'label' => 'Program Editions',
    ],

    'form' => [
        'fields' => [
            'linked_variant' => 'Linked Variant (Required for billing)',
        ],
        'feature_rules' => [
            'title'      => 'Edition Features',
            'add_action' => 'Add Feature Rule',
            'fields'     => [
                'feature'                      => 'Feature',
                'price'                        => 'Override Price',
                'auto_attach_on_final_license' => 'Auto Attach On Final License',
                'is_complimentary'             => 'Complimentary On First License',
                'invoice_on_initial_billing'   => 'Invoice On Initial Billing',
                'invoice_on_renewal'           => 'Invoice On Renewal',
                'auto_renew_with_license'      => 'Auto Renew With License',
            ],
            'helper_text' => [
                'price' => 'Leave empty to use the linked feature product price.',
            ],
        ],
        'helper_text' => [
            'linked_variant' => 'Choose the exact product variant that represents this edition.',
        ],
    ],

    'table' => [
        'columns' => [
            'program'         => 'Program',
            'name'            => 'Name',
            'variant'         => 'Variant',
            'max_devices'     => 'Max Devices',
            'license_price'   => 'License Price',
            'license_cost'    => 'License Cost',
            'monthly_renewal' => 'Monthly Renewal',
            'annual_renewal'  => 'Annual Renewal',
            'created_at'      => 'Created At',
            'updated_at'      => 'Updated At',
        ],
    ],
];
