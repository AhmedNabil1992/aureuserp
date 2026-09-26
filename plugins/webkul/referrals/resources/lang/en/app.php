<?php

return [
    'discount_types' => ['fixed' => 'Fixed amount', 'percentage' => 'Percentage'],
    'statuses'       => ['pending' => 'Pending payment', 'earned' => 'Earned', 'reversed' => 'Reversed', 'void' => 'Void'],
    'contexts'       => ['license' => 'Software licenses', 'online_subscription' => 'Online systems', 'sales_invoice' => 'Sales invoices'],
    'campaigns'      => ['title' => 'Referral Campaigns', 'singular' => 'Referral Campaign', 'rules' => 'Campaign Rules'],
    'codes'          => ['title' => 'Referral Codes', 'singular' => 'Referral Code', 'auto_help' => 'Leave blank to generate a secure code automatically.'],
    'redemptions'    => ['title' => 'Referral Redemptions', 'singular' => 'Referral Redemption', 'details' => 'Redemption Details'],
    'fields'         => [
        'name'            => 'Name', 'company' => 'Company', 'currency' => 'Currency', 'contexts' => 'Eligible sales paths',
        'products'        => 'Eligible products', 'discount_type' => 'Customer discount type', 'customer_discount' => 'Customer discount',
        'referrer_reward' => 'Referrer reward', 'minimum_eligible_amount' => 'Minimum eligible amount', 'journal' => 'Reward journal',
        'expense_account' => 'Referral marketing expense account', 'max_redemptions' => 'Maximum redemptions', 'starts_at' => 'Starts at',
        'ends_at'         => 'Ends at', 'first_purchase_only' => 'First qualifying purchase only', 'active' => 'Active', 'redemptions' => 'Redemptions',
        'customer'        => 'Customer', 'code' => 'Code', 'referred_customer' => 'New customer', 'referrer' => 'Referrer', 'campaign' => 'Campaign',
        'context'         => 'Sales path', 'invoice' => 'Invoice', 'reward_entry' => 'Reward journal entry', 'reward_reversal_entry' => 'Reward reversal entry',
    ],
    'customer' => [
        'navigation' => 'My Referral Code', 'title' => 'Invite Customers and Earn Credit',
        'share_help' => 'Share this code. Your reward becomes available after the qualifying invoice is paid.',
        'copy'       => 'Copy code', 'copied' => 'Code copied', 'earned' => 'Earned rewards', 'pending' => 'Pending rewards',
    ],
    'validation' => [
        'company_required'            => 'A company is required to create a referral code.',
        'draft_only'                  => 'A referral code can only be applied to a draft invoice.',
        'invalid_code'                => 'The referral code is invalid or belongs to another company.',
        'self_referral'               => 'Customers cannot use their own referral code.',
        'already_applied'             => 'Another referral code is already applied to this invoice.',
        'no_campaign'                 => 'No active referral campaign matches the selected products.',
        'minimum_not_met'             => 'The eligible products do not meet the campaign minimum.',
        'campaign_limit'              => 'This referral campaign has reached its maximum number of redemptions.',
        'first_purchase_only'         => 'This campaign is available only for the first qualifying purchase.',
        'discount_too_large'          => 'The referral discount exceeds the eligible product amount.',
        'receivable_account_required' => 'No customer receivable account is configured for the referrer.',
    ],
];
