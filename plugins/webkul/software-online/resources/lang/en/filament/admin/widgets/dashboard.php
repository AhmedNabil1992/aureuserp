<?php

return [
    'stats' => [
        'total' => [
            'label'       => 'Total Tenants',
            'description' => 'All online-system subscriptions',
        ],
        'active' => [
            'label'       => 'Active Tenants',
            'description' => ':percentage% of all tenants',
        ],
        'awaiting' => [
            'label'       => 'Awaiting Provisioning',
            'description' => ':failed failed operations need attention',
        ],
        'revenue' => [
            'label'       => 'Subscription Revenue',
            'description' => 'Total paid subscription transactions',
        ],
    ],
    'revenue' => [
        'heading' => 'Subscription trend over the last 12 months',
        'datasets'=> [
            'revenue'       => 'Subscription revenue',
            'subscriptions' => 'Subscription count',
        ],
    ],
    'status' => [
        'heading' => 'Tenants by status',
        'labels'  => [
            'active'    => 'Active',
            'awaiting'  => 'Awaiting provisioning',
            'suspended' => 'Suspended',
            'expired'   => 'Expired',
            'failed'    => 'Failed or deleting',
        ],
    ],
    'attention' => [
        'heading' => 'Subscriptions needing attention',
        'columns' => [
            'instance'   => 'Tenant',
            'customer'   => 'Customer',
            'status'     => 'Status',
            'expires_at' => 'Expires at',
            'amount'     => 'Amount',
        ],
    ],
    'syncs' => [
        'heading' => 'Recent synchronization activity',
        'columns' => [
            'instance'    => 'Tenant',
            'event'       => 'Operation',
            'status'      => 'Status',
            'occurred_at' => 'Time',
        ],
        'statuses' => [
            'received'  => 'Received',
            'processed' => 'Succeeded',
            'failed'    => 'Failed',
        ],
        'events' => [
            'tenant_provisioning_started' => 'Provisioning started',
            'tenant_provisioned'          => 'Provisioning completed',
            'tenant_provisioning_failed'  => 'Provisioning failed',
            'tenant_renewed'              => 'Subscription renewed',
            'tenant_suspended'            => 'Tenant suspended',
            'tenant_activated'            => 'Tenant activated',
            'tenant_deletion_started'     => 'Deletion started',
            'tenant_deleted'              => 'Tenant deleted',
            'tenant_entitlements_updated' => 'Entitlements updated',
        ],
    ],
];
