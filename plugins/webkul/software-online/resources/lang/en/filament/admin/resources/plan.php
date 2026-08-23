<?php

return [
    'navigation' => [
        'title' => 'System Plans & Tiers',
    ],
    'models' => [
        'singular' => 'System Plan',
        'plural'   => 'System Plans',
    ],
    'sections' => [
        'general'  => 'Plan Details',
        'pricing'  => 'Pricing & Quotas',
        'features' => 'Features & API Payload',
    ],
    'fields' => [
        'system'                    => 'Target System',
        'product'                   => 'Linked Service Product',
        'product_helper'            => 'Link this plan to a service product for automatic accounts invoicing.',
        'name'                      => 'Plan Name',
        'slug'                      => 'Slug',
        'description'               => 'Description',
        'monthly_price'             => 'Monthly Price',
        'annual_price'              => 'Annual Price',
        'trial_days'                => 'Trial Days',
        'max_users'                 => 'Max Users',
        'max_branches'              => 'Max Branches',
        'is_active'                 => 'Active',
        'instances_count'           => 'Active Subscriptions',
        'features_list'             => 'Features List',
        'custom_api_payload'        => 'Custom API Payload',
        'custom_api_payload_helper' => 'Custom keys sent to the remote API during provisioning.',
    ],
    'placeholders' => [
        'unlimited'       => 'Unlimited (Leave blank)',
        'feature_example' => 'POS terminal & E-Invoicing support',
    ],
];
