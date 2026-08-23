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
    ],
    'fields' => [
        'billing_cycle' => 'Renewal Period',
    ],
    'actions' => [
        'visit'      => 'Open Dashboard',
        'renew'      => 'Renew from Balance',
        'create_new' => 'Create New Website',
    ],
    'notifications' => [
        'renewed_success' => 'Subscription renewed successfully from balance',
        'renew_failed'    => 'Failed to renew subscription',
    ],
];
