<?php

return [
    'navigation' => ['group' => 'VPN Management'],
    'servers'    => ['title' => 'VPN Servers', 'singular' => 'VPN Server', 'connection' => 'SoftEther connection'],
    'console'    => ['title' => 'VPN Console'],
    'users'      => ['title' => 'Users', 'empty' => 'No users were returned for this hub.', 'search-placeholder' => 'Search users...'],
    'sessions'   => ['title' => 'Live sessions', 'empty' => 'There are no active sessions on this hub.', 'search-placeholder' => 'Search sessions...'],
    'details'    => ['title' => 'Details', 'empty' => 'No details are available.'],
    'fields'     => [
        'name'           => 'Name', 'host' => 'Host or IP', 'host_help' => 'Enter a hostname or IP only; HTTPS and /api/ are added automatically.',
        'port'           => 'HTTPS port', 'admin_password' => 'Server administrator password', 'password_help' => 'Encrypted at rest. Leave blank while editing to keep the current password.',
        'timeout'        => 'Timeout (seconds)', 'verify_tls' => 'Verify TLS certificate', 'active' => 'Active', 'hubs' => 'Hubs',
        'last_connected' => 'Last connected', 'last_error' => 'Last error', 'server' => 'Server', 'hub' => 'Virtual Hub',
        'username'       => 'Username', 'password' => 'Password', 'real_name' => 'Real name', 'group' => 'Group', 'expires_at' => 'Expires at', 'note' => 'Note',
        'auth_type'      => 'Authentication', 'logins' => 'Logins', 'last_login' => 'Last login', 'session' => 'Session',
        'ip_address'     => 'Client IP', 'started_at' => 'Started at', 'last_communication' => 'Last communication',
    ],
    'actions'  => ['add_server' => 'Add server', 'test' => 'Test connection', 'create_user' => 'Create user', 'refresh' => 'Refresh', 'details' => 'Details', 'delete' => 'Delete', 'disconnect' => 'Disconnect'],
    'messages' => [
        'connected'           => 'Connection successful', 'connection_failed' => 'Connection failed', 'operation_failed' => 'Operation failed',
        'user_created'        => 'User created', 'user_deleted' => 'User deleted', 'session_disconnected' => 'Session disconnected',
        'confirm_delete_user' => 'Delete this user from SoftEther?', 'confirm_disconnect' => 'Disconnect this active VPN session?',
    ],
    'common' => ['choose' => 'Choose…', 'never' => 'Never', 'actions' => 'Actions', 'yes' => 'Yes', 'no' => 'No'],
    'search' => ['no-results' => 'No matching results.'],
    'auth'   => ['0' => 'Anonymous', '1' => 'Password', '2' => 'User certificate', '3' => 'Root certificate', '4' => 'RADIUS', '5' => 'NT domain'],
];
