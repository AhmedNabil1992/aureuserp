<?php

return [
    'navigation' => [
        'title' => 'Systems & Platforms',
    ],
    'models' => [
        'singular' => 'Online System',
        'plural'   => 'Online Systems',
    ],
    'sections' => [
        'general'    => 'General Information',
        'api_config' => 'API Integration & Authentication',
    ],
    'fields' => [
        'name'              => 'System Name',
        'slug'              => 'Slug',
        'base_url'          => 'Base System URL',
        'base_url_helper'   => 'You can use {subdomain} as placeholder for client store domains.',
        'icon'              => 'Heroicon Identifier',
        'description'       => 'Description',
        'is_active'         => 'Is Active',
        'sort_order'        => 'Sort Order',
        'plans_count'       => 'Plans',
        'instances_count'   => 'Instances',
        'updated_at'        => 'Updated At',
        'api_base_url'      => 'API Base URL',
        'api_token'         => 'API Bearer Token',
        'api_headers'       => 'Custom Headers',
        'create_endpoint'   => 'Create Tenant Endpoint',
        'renew_endpoint'    => 'Renew Tenant Endpoint',
        'suspend_endpoint'  => 'Suspend Tenant Endpoint',
        'activate_endpoint' => 'Activate Tenant Endpoint',
        'sync_endpoint'     => 'Sync Status Endpoint',
    ],
    'actions' => [
        'test_api' => 'Test API Connection',
    ],
    'notifications' => [
        'api_connected' => 'API Connection Successful',
        'api_failed'    => 'API Connection Failed',
    ],
];
