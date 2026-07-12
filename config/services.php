<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
     * Firebase Web SDK config (used in browser JS for Realtime Database listeners).
     * These values come from your Firebase project's "Web App" config object.
     * They are safe to expose to the browser.
     */
    'firebase_web' => [
        'api_key'             => env('FIREBASE_WEB_API_KEY'),
        'auth_domain'         => env('FIREBASE_WEB_AUTH_DOMAIN'),
        'database_url'        => env('FIREBASE_WEB_DATABASE_URL'),
        'project_id'          => env('FIREBASE_WEB_PROJECT_ID'),
        'storage_bucket'      => env('FIREBASE_WEB_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_WEB_MESSAGING_SENDER_ID'),
        'app_id'              => env('FIREBASE_WEB_APP_ID'),
    ],

    'wifi_voucher' => [
        'endpoint'                       => env('WIFI_VOUCHER_API_ENDPOINT', 'https://cloud.etech-valley.com/cake4/rd_cake/vouchers/add.json'),
        'permanent_user_add_endpoint'    => env('WIFI_PERMANENT_USER_ADD_ENDPOINT', 'https://cloud.etech-valley.com/cake4/rd_cake/permanent-users/add.json'),
        'permanent_user_delete_endpoint' => env('WIFI_PERMANENT_USER_DELETE_ENDPOINT', 'https://cloud.etech-valley.com/cake4/rd_cake/permanent-users/delete.json'),
        'token'                          => env('WIFI_VOUCHER_API_TOKEN', '27cab0d2-77e3-4d78-aaab-b6356c1a4935'),
        'language'                       => env('WIFI_VOUCHER_API_LANGUAGE', '4_4'),
        'download_base_url'              => env('WIFI_VOUCHER_DOWNLOAD_BASE_URL', 'https://etech-valley.com/voucher'),
    ],

    'legacy_api' => [
        'key' => env('LEGACY_API_KEY', 'Justdoitnow157#'),
    ],

    'msg91' => [
        'auth_key'     => env('MSG91_AUTH_KEY'),
        'sender_id'    => env('MSG91_SENDER_ID', 'AUREUS'),
        'route'        => env('MSG91_ROUTE', '4'),
        'admin_mobile' => env('MSG91_ADMIN_MOBILE'),
    ],

];
