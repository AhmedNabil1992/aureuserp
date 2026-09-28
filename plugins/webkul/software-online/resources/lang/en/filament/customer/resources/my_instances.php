<?php

return [
    'navigation' => [
        'title' => 'My Online Websites',
    ],
    'models' => [
        'singular' => 'My Website',
        'plural'   => 'My Online Websites',
    ],
    'columns' => [
        'number'     => 'Instance #',
        'name'       => 'Website Name',
        'system'     => 'System',
        'plan'       => 'Plan',
        'status'     => 'Status',
        'expires_at' => 'Expires At',
        'url'        => 'Website URL',
        'price'      => 'Subscription Price',
        'auto_renew' => 'Automatic Renewal',
    ],
    'sections' => [
        'website'      => 'Website',
        'subscription' => 'Subscription',
    ],
    'values' => [
        'enabled'  => 'Enabled',
        'disabled' => 'Disabled',
    ],
    'fields' => [
        'billing_cycle' => 'Renewal Period',
        'periods'       => 'Number of Periods',
    ],
    'actions' => [
        'visit'      => 'Open Dashboard',
        'renew'      => 'Renew or Change Cycle from Balance',
        'create_new' => 'Create New Website',
    ],
    'notifications' => [
        'renewed_success' => 'Subscription renewed or changed successfully from balance',
        'renew_failed'    => 'Failed to renew subscription',
    ],
];
