<?php

return [
    'navigation' => [
        'title' => 'Voucher Batches',
        'group' => 'Network',
    ],

    'model-label'        => 'Voucher Batch',
    'plural-model-label' => 'Voucher Batches',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Batch Information',
                'fields' => [
                    'wifi_purchase_id'         => 'Invoice',
                    'cloud_id'                 => 'Cloud',
                    'realm_id'                 => 'Realm',
                    'nasidentifier'            => 'Access Point (NAS Identifier)',
                    'profile_id'               => 'Profile',
                    'validity'                 => 'Validity Period',
                    'days_valid'               => 'Days',
                    'hours_valid'              => 'Hours',
                    'minutes_valid'            => 'Minutes',
                    'batch_code'               => 'Batch Code',
                    'quantity'                 => 'Quantity',
                    'never_expire'             => 'Never Expire',
                    'never_expire_helper_text' => 'Auto-filled from invoice information',
                    'caption'                  => 'Caption',
                ],
                'buttons' => [
                    'new_batch' => 'Generate Vouchers',
                ],
            ],
        ],
    ],

    'messages' => [
        'generated_success' => 'Vouchers generated successfully.',
        'generated_warning' => 'Batch saved but voucher generation failed.',
    ],

    'table' => [
        'actions' => [
            'download_pdf' => 'Download PDF',
        ],
        'columns' => [
            'id'              => 'ID',
            'batch_code'      => 'Batch Code',
            'customer'        => 'Customer',
            'service_product' => 'Service Product',
            'cloud'           => 'Cloud',
            'access_point'    => 'Access Point',
            'quantity'        => 'Quantity',
            'never_expire'    => 'Never Expire',
            'created_at'      => 'Created At',
            'purchase'        => 'Purchase',
            'updated_at'      => 'Updated At',
        ],
    ],
];
