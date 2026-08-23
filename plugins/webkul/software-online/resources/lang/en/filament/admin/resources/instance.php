<?php

return [
    'navigation' => [
        'title' => 'Client Websites & Instances',
    ],
    'models' => [
        'singular' => 'Client Instance',
        'plural'   => 'Client Instances',
    ],
    'sections' => [
        'general'      => 'Instance & Partner Information',
        'subscription' => 'Subscription & Validity',
        'remote_sync'  => 'Remote API State & Sync',
    ],
    'fields' => [
        'instance_number'  => 'Instance #',
        'customer'         => 'Customer',
        'system'           => 'System',
        'plan'             => 'Plan',
        'name'             => 'Website / Store Name',
        'subdomain'        => 'Subdomain',
        'custom_domain'    => 'Custom Domain',
        'instance_url'     => 'Direct Access URL',
        'status'           => 'Status',
        'billing_cycle'    => 'Billing Cycle',
        'price'            => 'Price',
        'starts_at'        => 'Starts At',
        'expires_at'       => 'Expires At',
        'auto_renew'       => 'Auto Renew',
        'remote_tenant_id' => 'Remote Tenant ID',
        'last_api_error'   => 'Last API Error',
        'remote_data'      => 'Remote Response Payload',
    ],
    'actions' => [
        'visit_website' => 'Open Website',
        'provision_api' => 'Provision via API',
        'renew'         => 'Renew Subscription',
    ],
    'notifications' => [
        'provision_success' => 'Tenant provisioned successfully via API',
        'provision_failed'  => 'Failed to provision remote tenant',
        'renew_success'     => 'Subscription renewed successfully',
        'renew_failed'      => 'Failed to renew subscription',
    ],
];
