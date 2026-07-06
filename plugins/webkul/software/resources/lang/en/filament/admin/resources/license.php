<?php

return [
    'navigation' => [
        'label' => 'Licenses',
    ],

    'table' => [
        'columns' => [
            'program' => 'Program',
            'edition' => 'Edition',
            'partner' => 'Partner',
            'partner_phone' => 'Partner Phone',
            'state' => 'State',
            'city' => 'City',
            'approver' => 'Approver',
        ],
    ],

    'actions' => [
        'bill_license' => 'Bill License',
        'renew' => 'Renew',
        'activate' => 'Activate',
        'deactivate' => 'Deactivate',
        'expire' => 'Expire',
        'edition' => 'Edition',
        'type' => 'Type',
    ],

    'notifications' => [
        'trial_activated' => 'Trial license activated successfully',
        'trial_expires_on' => 'Trial will expire on :date',
        'invoice_created' => 'Invoice created successfully',
        'invoice_number' => 'Invoice No: :number',
        'invoice_failed' => 'Failed to create invoice',
        'renew_success' => 'License renewed successfully',
        'renew_failed' => 'Failed to renew license',
        'activate_success' => 'License activated successfully',
        'activate_failed' => 'Failed to activate license',
        'deactivate_success' => 'License deactivated successfully',
        'deactivate_failed' => 'Failed to deactivate license',
        'expire_success' => 'License expired successfully',
        'expire_failed' => 'Failed to expire license',
    ],
];
